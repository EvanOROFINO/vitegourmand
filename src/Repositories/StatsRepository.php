<?php
/**
 * Modèle de statistiques NoSQL (MongoDB).
 * Stocke et récupère les données pour le dashboard administrateur :
 * - Compteur de commandes par menu (collection commandes_par_menu)
 * - Chiffre d'affaires par menu et par période (collection ca_par_menu)
 * - Logs d'activité (collection logs)
 *
 * Conformément à l'énoncé : « Les données doivent venir d'une base de données non relationnelle ».
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Mongo;
use MongoDB\BSON\UTCDateTime;

final class StatsRepository
{
    /**
     * Incrémente le compteur de commandes pour un menu donné.
     * Appelé à chaque création de commande.
     */
    public static function incrementCommandeMenu(int $menuId, string $titre, float $montant): void
    {
        $db = Mongo::getInstance();
        $db->commandes_par_menu->updateOne(
            ['menu_id' => $menuId],
            [
                '$set'  => ['titre' => $titre],
                '$inc'  => ['nb_commandes' => 1, 'ca_total' => $montant],
                '$push' => ['historique' => [
                    'date'    => new UTCDateTime(),
                    'montant' => $montant,
                ]],
            ],
            ['upsert' => true],
        );
    }

    /**
     * Retourne les statistiques de commandes par menu (pour graphique admin).
     */
    public static function commandesParMenu(): array
    {
        $db = Mongo::getInstance();
        $cursor = $db->commandes_par_menu->find([], ['sort' => ['nb_commandes' => -1]]);

        $result = [];
        foreach ($cursor as $doc) {
            $result[] = [
                'menu_id'      => $doc['menu_id'] ?? 0,
                'titre'        => $doc['titre'] ?? '—',
                'nb_commandes' => $doc['nb_commandes'] ?? 0,
                'ca_total'     => round($doc['ca_total'] ?? 0, 2),
            ];
        }
        return $result;
    }

    /**
     * Calcule le chiffre d'affaires par menu sur une période donnée.
     */
    public static function chiffreAffairesParMenu(?string $dateDebut = null, ?string $dateFin = null, ?int $menuId = null): array
    {
        $db = Mongo::getInstance();

        $match = [];
        if ($menuId) {
            $match['menu_id'] = $menuId;
        }

        $cursor = $db->commandes_par_menu->find($match);

        $result = [];
        foreach ($cursor as $doc) {
            $caPeriode = 0;
            $nbPeriode = 0;
            if (isset($doc['historique'])) {
                foreach ($doc['historique'] as $h) {
                    $date = $h['date']->toDateTime()->format('Y-m-d');
                    if ($dateDebut && $date < $dateDebut) continue;
                    if ($dateFin    && $date > $dateFin)    continue;
                    $caPeriode += $h['montant'];
                    $nbPeriode += 1;
                }
            }
            $result[] = [
                'menu_id'      => $doc['menu_id'] ?? 0,
                'titre'        => $doc['titre'] ?? '—',
                'nb_commandes' => $nbPeriode,
                'ca_total'     => round($caPeriode, 2),
            ];
        }
        return $result;
    }

    /**
     * Log d'activité (audit trail).
     */
    public static function log(string $event, array $context = []): void
    {
        $db = Mongo::getInstance();
        $db->logs->insertOne([
            'event'   => $event,
            'context' => $context,
            'date'    => new UTCDateTime(),
            'ip'      => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    }
}
