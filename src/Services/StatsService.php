<?php
/**
 * Service métier — Statistiques agrégées pour le dashboard administrateur.
 *
 * Délègue le stockage (MongoDB + MariaDB) aux Repositories et expose des
 * agrégations métier prêtes à être consommées par les contrôleurs et les vues.
 */

declare(strict_types=1);

namespace App\Services;

use App\Repositories\StatsRepository;

final class StatsService
{
    /**
     * Statistiques globales pour le tableau de bord admin.
     *
     * @return array{
     *   commandes_par_menu: array,
     *   ca_par_menu: array
     * }
     */
    public function dashboard(): array
    {
        return [
            'commandes_par_menu' => StatsRepository::commandesParMenu(),
            'ca_par_menu'        => StatsRepository::chiffreAffairesParMenu(),
        ];
    }

    /**
     * CA filtré par période ou menu (utilisé par l'admin).
     */
    public function chiffreAffaires(?string $dateDebut = null, ?string $dateFin = null, ?int $menuId = null): array
    {
        return StatsRepository::chiffreAffairesParMenu($dateDebut, $dateFin, $menuId);
    }
}
