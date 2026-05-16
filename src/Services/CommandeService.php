<?php
/**
 * Service métier — Orchestration des commandes.
 *
 * Centralise les règles de validation, la création, la modification et les transitions
 * de statut des commandes. Le controller HTTP ne contient plus que la logique d'entrée/sortie.
 *
 * Cette couche est ce qui sépare clairement :
 *   - Controllers (HTTP, validation des inputs, redirections)
 *   - Services (règles métier, orchestration)
 *   - Repositories (accès aux données)
 */

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CommandeRepository;
use App\Repositories\MenuRepository;
use App\Repositories\StatsRepository;
use DomainException;

final class CommandeService
{
    public function __construct(
        private CommandeRepository $commandes = new CommandeRepository(),
        private MenuRepository $menus = new MenuRepository(),
        private PriceCalculatorService $calculator = new PriceCalculatorService(),
    ) {}

    /**
     * Crée une nouvelle commande après application de toutes les règles métier.
     *
     * @throws DomainException si une règle métier est violée
     */
    public function creerCommande(array $data, int $userId): array
    {
        $menu = MenuRepository::find((int) $data['menu_id']);
        if (!$menu || !$menu['actif']) {
            throw new DomainException('Menu introuvable ou inactif.');
        }

        $nbPersonnes = (int) ($data['nombre_personne'] ?? 0);
        $datePrestation = (string) $data['date_prestation'];
        $heure = (string) $data['heure_livraison'];
        $adresse = (string) $data['adresse_livraison'];
        $ville = (string) $data['ville_livraison'];
        $distanceKm = (float) ($data['distance_km'] ?? 0);

        // --- Règles métier de validation ---
        if ($datePrestation === '' || $heure === '' || $adresse === '' || $ville === '') {
            throw new DomainException('Tous les champs sont obligatoires.');
        }
        if ($nbPersonnes < (int) $menu['nombre_personne_minimum']) {
            throw new DomainException('Le nombre de personnes minimum est de ' . $menu['nombre_personne_minimum'] . '.');
        }
        if ((int) $menu['quantite_restante'] <= 0) {
            throw new DomainException('Ce menu n\'est plus disponible.');
        }
        if (strtotime($datePrestation) < strtotime('+1 day')) {
            throw new DomainException('La date de prestation doit être au moins le lendemain.');
        }

        // --- Calcul du prix (service dédié) ---
        $prix = $this->calculator->calculer($menu, $nbPersonnes, $ville, $distanceKm);

        // --- Persistance ---
        $numero = numero_commande();
        CommandeRepository::create([
            'numero_commande'    => $numero,
            'utilisateur_id'     => $userId,
            'menu_id'            => $menu['menu_id'],
            'date_prestation'    => $datePrestation,
            'heure_livraison'    => $heure,
            'adresse_livraison'  => $adresse,
            'ville_livraison'    => $ville,
            'distance_km'        => $distanceKm,
            'nombre_personne'    => $nbPersonnes,
            'prix_menu'          => $prix['prix_menu'],
            'prix_livraison'     => $prix['prix_livraison'],
            'reduction'          => $prix['reduction'],
            'prix_total'         => $prix['prix_total'],
        ]);

        // --- Effets de bord (statistiques NoSQL) ---
        StatsRepository::incrementCommandeMenu((int) $menu['menu_id'], $menu['titre'], $prix['prix_total']);
        StatsRepository::log('commande_creee', ['numero' => $numero, 'user_id' => $userId]);

        return [
            'numero' => $numero,
            'menu'   => $menu,
            'prix'   => $prix,
        ];
    }

    /**
     * Calcule le prix sans créer la commande (utilisé par l'endpoint AJAX).
     *
     * @throws DomainException si le menu est introuvable ou nb_personnes invalide
     */
    public function previewPrix(int $menuId, int $nbPersonnes, string $ville, float $distanceKm): array
    {
        $menu = MenuRepository::find($menuId);
        if (!$menu) {
            throw new DomainException('Menu introuvable.');
        }
        if ($nbPersonnes < (int) $menu['nombre_personne_minimum']) {
            throw new DomainException('Nombre de personnes inférieur au minimum requis (' . $menu['nombre_personne_minimum'] . ').');
        }

        return $this->calculator->calculer($menu, $nbPersonnes, $ville, $distanceKm);
    }

    /**
     * Transition de statut métier — refuse les transitions invalides.
     */
    public function changerStatut(string $numero, string $nouveauStatut, ?string $commentaire = null): void
    {
        $allowed = ['en_attente', 'acceptee', 'en_preparation', 'en_livraison', 'livree', 'retour_materiel', 'terminee', 'annulee'];
        if (!in_array($nouveauStatut, $allowed, true)) {
            throw new DomainException('Statut invalide : ' . $nouveauStatut);
        }

        CommandeRepository::changerStatut($numero, $nouveauStatut, $commentaire);
        StatsRepository::log('commande_statut_change', ['numero' => $numero, 'statut' => $nouveauStatut]);
    }
}
