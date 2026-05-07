<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Horaire
{
    public static function all(): array
    {
        return Database::getInstance()
            ->query('SELECT * FROM horaire ORDER BY ordre')
            ->fetchAll();
    }

    public static function update(int $id, string $jour, string $ouverture, string $fermeture): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE horaire SET jour = :j, heure_ouverture = :o, heure_fermeture = :f WHERE horaire_id = :id');
        return $stmt->execute([':id' => $id, ':j' => $jour, ':o' => $ouverture, ':f' => $fermeture]);
    }
}
