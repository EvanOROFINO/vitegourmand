<?php
/**
 * Modèle commande — règles métier (calcul prix, statuts, etc.).
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Commande
{
    /**
     * Calcule le prix d'une commande selon les règles Vite & Gourmand :
     * - Frais livraison = 5€ + 0,59€/km hors Bordeaux
     * - Réduction = -10% si nb_personnes >= minimum + 5
     */
    public static function calculerPrix(array $menu, int $nbPersonnes, string $ville, float $distanceKm = 0): array
    {
        $config = require __DIR__ . '/../../config/config.php';
        $b = $config['business'];

        $prixMenu = $menu['prix_par_personne'] * $nbPersonnes;

        $reduction = 0;
        if ($nbPersonnes >= $menu['nombre_personne_minimum'] + $b['reduction_seuil']) {
            $reduction = round($prixMenu * $b['reduction_taux'], 2);
        }

        $prixLivraison = 0;
        if (strcasecmp(trim($ville), $b['ville_locale']) !== 0 && $distanceKm > 0) {
            $prixLivraison = $b['frais_livraison_base'] + $b['frais_par_km'] * $distanceKm;
            $prixLivraison = round($prixLivraison, 2);
        }

        $prixTotal = round($prixMenu - $reduction + $prixLivraison, 2);

        return [
            'prix_menu'      => round($prixMenu, 2),
            'reduction'      => $reduction,
            'prix_livraison' => $prixLivraison,
            'prix_total'     => $prixTotal,
        ];
    }

    public static function create(array $data): string
    {
        $pdo = Database::getInstance();
        $numero = $data['numero_commande'];

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('
                INSERT INTO commande (numero_commande, utilisateur_id, menu_id, date_prestation, heure_livraison,
                    adresse_livraison, ville_livraison, distance_km, nombre_personne, prix_menu, prix_livraison,
                    reduction, prix_total, statut)
                VALUES (:num, :uid, :mid, :datep, :heure, :adr, :ville, :dist, :nb, :pm, :pl, :red, :pt, "en_attente")
            ');
            $stmt->execute([
                ':num'   => $numero,
                ':uid'   => (int) $data['utilisateur_id'],
                ':mid'   => (int) $data['menu_id'],
                ':datep' => $data['date_prestation'],
                ':heure' => $data['heure_livraison'],
                ':adr'   => $data['adresse_livraison'],
                ':ville' => $data['ville_livraison'],
                ':dist'  => (float) ($data['distance_km'] ?? 0),
                ':nb'    => (int) $data['nombre_personne'],
                ':pm'    => (float) $data['prix_menu'],
                ':pl'    => (float) $data['prix_livraison'],
                ':red'   => (float) $data['reduction'],
                ':pt'    => (float) $data['prix_total'],
            ]);

            $h = $pdo->prepare('INSERT INTO commande_statut_historique (numero_commande, statut) VALUES (:n, "en_attente")');
            $h->execute([':n' => $numero]);

            $pdo->commit();
            return $numero;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function find(string $numero): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('
            SELECT c.*, m.titre AS menu_titre, m.conditions_menu, u.email, u.prenom, u.nom, u.telephone
            FROM commande c
            INNER JOIN menu m       ON c.menu_id = m.menu_id
            INNER JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            WHERE c.numero_commande = :n LIMIT 1
        ');
        $stmt->execute([':n' => $numero]);
        return $stmt->fetch() ?: null;
    }

    public static function findByUser(int $userId): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('
            SELECT c.*, m.titre AS menu_titre
            FROM commande c
            INNER JOIN menu m ON c.menu_id = m.menu_id
            WHERE c.utilisateur_id = :uid
            ORDER BY c.date_commande DESC
        ');
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    /** Liste toutes les commandes pour les employés/admin avec filtres facultatifs. */
    public static function listForStaff(?string $statut = null, ?string $rechercheClient = null): array
    {
        $pdo = Database::getInstance();
        $sql = '
            SELECT c.*, m.titre AS menu_titre, u.email, u.prenom, u.nom
            FROM commande c
            INNER JOIN menu m       ON c.menu_id = m.menu_id
            INNER JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            WHERE 1=1
        ';
        $params = [];
        if ($statut) {
            $sql .= ' AND c.statut = :statut';
            $params[':statut'] = $statut;
        }
        if ($rechercheClient) {
            $sql .= ' AND (u.email LIKE :q OR u.nom LIKE :q OR u.prenom LIKE :q OR c.numero_commande LIKE :q)';
            $params[':q'] = '%' . $rechercheClient . '%';
        }
        $sql .= ' ORDER BY c.date_commande DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function changerStatut(string $numero, string $nouveauStatut, ?string $commentaire = null): bool
    {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('UPDATE commande SET statut = :s WHERE numero_commande = :n');
            $stmt->execute([':s' => $nouveauStatut, ':n' => $numero]);

            $h = $pdo->prepare('INSERT INTO commande_statut_historique (numero_commande, statut, commentaire) VALUES (:n, :s, :c)');
            $h->execute([':n' => $numero, ':s' => $nouveauStatut, ':c' => $commentaire]);

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function annuler(string $numero, string $motif, string $modeContact): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE commande SET statut = "annulee", motif_annulation = :m, mode_contact = :c WHERE numero_commande = :n');
        $stmt->execute([':n' => $numero, ':m' => $motif, ':c' => $modeContact]);

        $h = $pdo->prepare('INSERT INTO commande_statut_historique (numero_commande, statut, commentaire) VALUES (:n, "annulee", :c)');
        return $h->execute([':n' => $numero, ':c' => $motif]);
    }

    public static function modifier(string $numero, array $data): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('
            UPDATE commande SET
                date_prestation = :datep,
                heure_livraison = :heure,
                adresse_livraison = :adr,
                ville_livraison = :ville,
                distance_km = :dist,
                nombre_personne = :nb,
                prix_menu = :pm,
                prix_livraison = :pl,
                reduction = :red,
                prix_total = :pt
            WHERE numero_commande = :n AND statut = "en_attente"
        ');
        return $stmt->execute([
            ':n'     => $numero,
            ':datep' => $data['date_prestation'],
            ':heure' => $data['heure_livraison'],
            ':adr'   => $data['adresse_livraison'],
            ':ville' => $data['ville_livraison'],
            ':dist'  => (float) $data['distance_km'],
            ':nb'    => (int) $data['nombre_personne'],
            ':pm'    => (float) $data['prix_menu'],
            ':pl'    => (float) $data['prix_livraison'],
            ':red'   => (float) $data['reduction'],
            ':pt'    => (float) $data['prix_total'],
        ]);
    }

    public static function historique(string $numero): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM commande_statut_historique WHERE numero_commande = :n ORDER BY date_changement ASC');
        $stmt->execute([':n' => $numero]);
        return $stmt->fetchAll();
    }
}
