<?php
/**
 * Service métier — Calcul du prix d'une commande.
 *
 * Encapsule les règles métier de tarification définies dans le cahier des charges :
 *   - Frais de livraison = 5 € + 0,59 €/km hors Bordeaux
 *   - Réduction = -10 % si nb_personnes ≥ nb_personne_minimum + 5
 *
 * Aucune dépendance à la BDD : ce service est pur, testable unitairement.
 */

declare(strict_types=1);

namespace App\Services;

final class PriceCalculatorService
{
    /** @var array<string,mixed> Règles métier injectées (depuis config/config.php). */
    private array $rules;

    public function __construct(?array $rules = null)
    {
        if ($rules === null) {
            $config = require __DIR__ . '/../../config/config.php';
            $rules = $config['business'];
        }
        $this->rules = $rules;
    }

    /**
     * Calcule le détail de prix d'une commande.
     *
     * @param array $menu        Menu (doit contenir prix_par_personne, nombre_personne_minimum)
     * @param int   $nbPersonnes Nombre de personnes pour la commande
     * @param string $ville      Ville de livraison (comparée à la ville locale)
     * @param float $distanceKm  Distance en km (utilisée hors Bordeaux)
     *
     * @return array{prix_menu:float, reduction:float, prix_livraison:float, prix_total:float}
     */
    public function calculer(array $menu, int $nbPersonnes, string $ville, float $distanceKm = 0): array
    {
        $prixMenu = (float) $menu['prix_par_personne'] * $nbPersonnes;

        $reduction = $this->calculerReduction($menu, $nbPersonnes, $prixMenu);
        $prixLivraison = $this->calculerLivraison($ville, $distanceKm);

        $prixTotal = round($prixMenu - $reduction + $prixLivraison, 2);

        return [
            'prix_menu'      => round($prixMenu, 2),
            'reduction'      => $reduction,
            'prix_livraison' => $prixLivraison,
            'prix_total'     => $prixTotal,
        ];
    }

    /**
     * Calcule la réduction : -10 % si nb_personnes ≥ minimum + seuil (par défaut +5).
     */
    private function calculerReduction(array $menu, int $nbPersonnes, float $prixMenu): float
    {
        $seuil = (int) $this->rules['reduction_seuil'];
        $minimum = (int) ($menu['nombre_personne_minimum'] ?? 0);

        if ($nbPersonnes >= $minimum + $seuil) {
            return round($prixMenu * (float) $this->rules['reduction_taux'], 2);
        }
        return 0.0;
    }

    /**
     * Frais de livraison : 0 € en ville locale, sinon 5 € + 0,59 €/km.
     */
    private function calculerLivraison(string $ville, float $distanceKm): float
    {
        $villeLocale = (string) $this->rules['ville_locale'];

        if (strcasecmp(trim($ville), $villeLocale) === 0 || $distanceKm <= 0) {
            return 0.0;
        }

        $base = (float) $this->rules['frais_livraison_base'];
        $parKm = (float) $this->rules['frais_par_km'];

        return round($base + $parKm * $distanceKm, 2);
    }
}
