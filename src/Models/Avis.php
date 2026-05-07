<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Avis
{
    public static function create(int $userId, string $numeroCommande, int $note, string $description): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('
            INSERT INTO avis (utilisateur_id, numero_commande, note, description)
            VALUES (:u, :n, :note, :d)
        ');
        return $stmt->execute([':u' => $userId, ':n' => $numeroCommande, ':note' => $note, ':d' => $description]);
    }

    public static function valides(int $limit = 10): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('
            SELECT a.*, u.prenom, u.nom
            FROM avis a
            INNER JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
            WHERE a.statut = "valide"
            ORDER BY a.created_at DESC
            LIMIT :l
        ');
        $stmt->bindValue(':l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function enAttente(): array
    {
        $pdo = Database::getInstance();
        return $pdo->query('
            SELECT a.*, u.prenom, u.nom, u.email
            FROM avis a
            INNER JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
            WHERE a.statut = "en_attente"
            ORDER BY a.created_at DESC
        ')->fetchAll();
    }

    public static function valider(int $avisId): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE avis SET statut = "valide" WHERE avis_id = :id');
        return $stmt->execute([':id' => $avisId]);
    }

    public static function refuser(int $avisId): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE avis SET statut = "refuse" WHERE avis_id = :id');
        return $stmt->execute([':id' => $avisId]);
    }

    public static function findByCommande(string $numero): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM avis WHERE numero_commande = :n LIMIT 1');
        $stmt->execute([':n' => $numero]);
        return $stmt->fetch() ?: null;
    }
}
