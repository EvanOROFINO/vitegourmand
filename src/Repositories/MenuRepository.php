<?php
/**
 * Modèle menu — accès aux données de la table menu et associations.
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class MenuRepository
{
    /**
     * Liste des menus avec filtres optionnels.
     *
     * @param array{prix_max?:float, prix_min?:float, theme?:int, regime?:int, personnes?:int} $filters
     */
    public static function search(array $filters = []): array
    {
        $pdo = Database::getInstance();

        $sql = '
            SELECT m.*, t.libelle AS theme_libelle, r.libelle AS regime_libelle,
                   (SELECT chemin FROM menu_image WHERE menu_id = m.menu_id ORDER BY ordre ASC LIMIT 1) AS image_principale
            FROM menu m
            INNER JOIN theme t  ON m.theme_id  = t.theme_id
            INNER JOIN regime r ON m.regime_id = r.regime_id
            WHERE m.actif = TRUE
        ';
        $params = [];

        if (!empty($filters['prix_max'])) {
            $sql .= ' AND m.prix_par_personne <= :prix_max';
            $params[':prix_max'] = $filters['prix_max'];
        }
        if (!empty($filters['prix_min'])) {
            $sql .= ' AND m.prix_par_personne >= :prix_min';
            $params[':prix_min'] = $filters['prix_min'];
        }
        if (!empty($filters['theme'])) {
            $sql .= ' AND m.theme_id = :theme';
            $params[':theme'] = (int) $filters['theme'];
        }
        if (!empty($filters['regime'])) {
            $sql .= ' AND m.regime_id = :regime';
            $params[':regime'] = (int) $filters['regime'];
        }
        if (!empty($filters['personnes'])) {
            $sql .= ' AND m.nombre_personne_minimum <= :personnes';
            $params[':personnes'] = (int) $filters['personnes'];
        }

        $sql .= ' ORDER BY m.titre ASC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('
            SELECT m.*, t.libelle AS theme_libelle, r.libelle AS regime_libelle
            FROM menu m
            INNER JOIN theme t  ON m.theme_id  = t.theme_id
            INNER JOIN regime r ON m.regime_id = r.regime_id
            WHERE m.menu_id = :id
            LIMIT 1
        ');
        $stmt->execute([':id' => $id]);
        $menu = $stmt->fetch();
        if (!$menu) {
            return null;
        }

        $menu['images']     = self::getImages($id);
        $menu['plats']      = self::getPlats($id);
        $menu['allergenes'] = self::getAllergenesAggreges($id);

        return $menu;
    }

    public static function getImages(int $menuId): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM menu_image WHERE menu_id = :id ORDER BY ordre ASC');
        $stmt->execute([':id' => $menuId]);
        return $stmt->fetchAll();
    }

    public static function getPlats(int $menuId): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('
            SELECT p.*, GROUP_CONCAT(a.libelle SEPARATOR ", ") AS allergenes
            FROM plat p
            INNER JOIN menu_plat mp        ON p.plat_id = mp.plat_id
            LEFT  JOIN plat_allergene pa   ON p.plat_id = pa.plat_id
            LEFT  JOIN allergene a         ON pa.allergene_id = a.allergene_id
            WHERE mp.menu_id = :id
            GROUP BY p.plat_id
            ORDER BY FIELD(p.type, "entree", "plat", "dessert"), p.titre
        ');
        $stmt->execute([':id' => $menuId]);
        return $stmt->fetchAll();
    }

    /** Liste agrégée des allergènes présents dans tous les plats du menu. */
    public static function getAllergenesAggreges(int $menuId): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('
            SELECT DISTINCT a.libelle
            FROM allergene a
            INNER JOIN plat_allergene pa ON a.allergene_id = pa.allergene_id
            INNER JOIN menu_plat mp      ON pa.plat_id = mp.plat_id
            WHERE mp.menu_id = :id
            ORDER BY a.libelle
        ');
        $stmt->execute([':id' => $menuId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function create(array $data): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('
            INSERT INTO menu (titre, description, nombre_personne_minimum, prix_par_personne, conditions_menu, quantite_restante, theme_id, regime_id, actif)
            VALUES (:titre, :desc, :min, :prix, :cond, :qte, :theme, :regime, :actif)
        ');
        $stmt->execute([
            ':titre'  => $data['titre'],
            ':desc'   => $data['description'],
            ':min'    => (int) $data['nombre_personne_minimum'],
            ':prix'   => (float) $data['prix_par_personne'],
            ':cond'   => $data['conditions_menu'] ?? null,
            ':qte'    => (int) ($data['quantite_restante'] ?? 0),
            ':theme'  => (int) $data['theme_id'],
            ':regime' => (int) $data['regime_id'],
            ':actif'  => !empty($data['actif']) ? 1 : 0,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('
            UPDATE menu SET
                titre = :titre,
                description = :desc,
                nombre_personne_minimum = :min,
                prix_par_personne = :prix,
                conditions_menu = :cond,
                quantite_restante = :qte,
                theme_id = :theme,
                regime_id = :regime,
                actif = :actif
            WHERE menu_id = :id
        ');
        return $stmt->execute([
            ':id'     => $id,
            ':titre'  => $data['titre'],
            ':desc'   => $data['description'],
            ':min'    => (int) $data['nombre_personne_minimum'],
            ':prix'   => (float) $data['prix_par_personne'],
            ':cond'   => $data['conditions_menu'] ?? null,
            ':qte'    => (int) ($data['quantite_restante'] ?? 0),
            ':theme'  => (int) $data['theme_id'],
            ':regime' => (int) $data['regime_id'],
            ':actif'  => !empty($data['actif']) ? 1 : 0,
        ]);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE menu SET actif = FALSE WHERE menu_id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public static function setPlats(int $menuId, array $platIds): void
    {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();
        try {
            $pdo->prepare('DELETE FROM menu_plat WHERE menu_id = :id')->execute([':id' => $menuId]);
            $stmt = $pdo->prepare('INSERT INTO menu_plat (menu_id, plat_id) VALUES (:m, :p)');
            foreach ($platIds as $pid) {
                $stmt->execute([':m' => $menuId, ':p' => (int) $pid]);
            }
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
