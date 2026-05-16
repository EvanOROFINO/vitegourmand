<?php
/**
 * Service métier — Modération des avis client.
 *
 * Les avis créés par les utilisateurs ne sont visibles publiquement qu'après validation
 * par un employé. Ce service centralise les règles métier de création et modération.
 */

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AvisRepository;
use App\Repositories\StatsRepository;
use DomainException;

final class AvisService
{
    /**
     * Crée un nouvel avis client en attente de modération.
     *
     * @throws DomainException Si la note est invalide ou le commentaire trop court.
     */
    public function creerAvis(int $userId, string $numeroCommande, int $note, string $description): void
    {
        if ($note < 1 || $note > 5) {
            throw new DomainException('La note doit être comprise entre 1 et 5.');
        }
        if (mb_strlen(trim($description)) < 5) {
            throw new DomainException('Le commentaire doit contenir au moins 5 caractères.');
        }

        AvisRepository::create($userId, $numeroCommande, $note, trim($description));
        StatsRepository::log('avis_cree', [
            'user_id'  => $userId,
            'commande' => $numeroCommande,
            'note'     => $note,
        ]);
    }

    /**
     * Valide ou refuse un avis (employé uniquement).
     */
    public function moderer(int $avisId, bool $accepter, int $employeId): void
    {
        if ($accepter) {
            AvisRepository::valider($avisId);
            StatsRepository::log('avis_valide', ['avis_id' => $avisId, 'employe_id' => $employeId]);
        } else {
            AvisRepository::refuser($avisId);
            StatsRepository::log('avis_refuse', ['avis_id' => $avisId, 'employe_id' => $employeId]);
        }
    }

    public function listerEnAttente(): array
    {
        return AvisRepository::enAttente();
    }

    public function listerPublics(int $limite = 10): array
    {
        return AvisRepository::valides($limite);
    }
}
