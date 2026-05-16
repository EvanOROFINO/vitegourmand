<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class PlatRepository
{
    public static function all(): array
    {
        $pdo = Database::getInstance();
        return $pdo->query('SELECT * FROM plat ORDER BY FIELD(type, "entree", "plat", "dessert"), titre')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM plat WHERE plat_id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('INSERT INTO plat (titre, photo, type) VALUES (:t, :p, :type)');
        $stmt->execute([
            ':t'    => $data['titre'],
            ':p'    => $data['photo'] ?? null,
            ':type' => $data['type'],
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE plat SET titre = :t, photo = :p, type = :type WHERE plat_id = :id');
        return $stmt->execute([
            ':id'   => $id,
            ':t'    => $data['titre'],
            ':p'    => $data['photo'] ?? null,
            ':type' => $data['type'],
        ]);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('DELETE FROM plat WHERE plat_id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
