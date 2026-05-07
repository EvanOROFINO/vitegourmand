<?php
/**
 * Modèle utilisateur — accès aux données de la table utilisateur et possede.
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class User
{
    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM utilisateur WHERE email = :email AND actif = TRUE LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            return null;
        }

        $user['roles'] = self::getRoles((int) $user['utilisateur_id']);
        return $user;
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM utilisateur WHERE utilisateur_id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();

        if (!$user) {
            return null;
        }

        $user['roles'] = self::getRoles($id);
        return $user;
    }

    public static function getRoles(int $userId): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('
            SELECT r.libelle FROM role r
            INNER JOIN possede p ON r.role_id = p.role_id
            WHERE p.utilisateur_id = :id
        ');
        $stmt->execute([':id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function create(array $data, array $roles = ['utilisateur']): int
    {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare('
                INSERT INTO utilisateur (email, password, prenom, nom, telephone, adresse_postale, ville, pays)
                VALUES (:email, :password, :prenom, :nom, :telephone, :adresse, :ville, :pays)
            ');
            $stmt->execute([
                ':email'     => $data['email'],
                ':password'  => $data['password'],
                ':prenom'    => $data['prenom'],
                ':nom'       => $data['nom'],
                ':telephone' => $data['telephone'],
                ':adresse'   => $data['adresse_postale'],
                ':ville'     => $data['ville'] ?? 'Bordeaux',
                ':pays'      => $data['pays'] ?? 'France',
            ]);
            $userId = (int) $pdo->lastInsertId();

            foreach ($roles as $role) {
                $rstmt = $pdo->prepare('
                    INSERT INTO possede (utilisateur_id, role_id)
                    SELECT :uid, role_id FROM role WHERE libelle = :role
                ');
                $rstmt->execute([':uid' => $userId, ':role' => $role]);
            }

            $pdo->commit();
            return $userId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('
            UPDATE utilisateur SET
                prenom = :prenom,
                nom = :nom,
                telephone = :telephone,
                adresse_postale = :adresse,
                ville = :ville,
                pays = :pays
            WHERE utilisateur_id = :id
        ');
        return $stmt->execute([
            ':id'        => $id,
            ':prenom'    => $data['prenom'],
            ':nom'       => $data['nom'],
            ':telephone' => $data['telephone'],
            ':adresse'   => $data['adresse_postale'],
            ':ville'     => $data['ville'] ?? 'Bordeaux',
            ':pays'      => $data['pays'] ?? 'France',
        ]);
    }

    public static function setResetToken(int $userId, string $token, string $expires): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE utilisateur SET reset_token = :t, reset_expires = :e WHERE utilisateur_id = :id');
        return $stmt->execute([':t' => $token, ':e' => $expires, ':id' => $userId]);
    }

    public static function findByResetToken(string $token): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM utilisateur WHERE reset_token = :t AND reset_expires > NOW() LIMIT 1');
        $stmt->execute([':t' => $token]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function updatePassword(int $userId, string $hash): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE utilisateur SET password = :p, reset_token = NULL, reset_expires = NULL WHERE utilisateur_id = :id');
        return $stmt->execute([':p' => $hash, ':id' => $userId]);
    }

    public static function setActive(int $userId, bool $active): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE utilisateur SET actif = :a WHERE utilisateur_id = :id');
        return $stmt->execute([':a' => $active ? 1 : 0, ':id' => $userId]);
    }

    /** Retourne tous les utilisateurs ayant le rôle "employe" (pour l'admin). */
    public static function listEmployes(): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->query('
            SELECT u.* FROM utilisateur u
            INNER JOIN possede p ON u.utilisateur_id = p.utilisateur_id
            INNER JOIN role r    ON p.role_id = r.role_id
            WHERE r.libelle = "employe"
            ORDER BY u.created_at DESC
        ');
        return $stmt->fetchAll();
    }
}
