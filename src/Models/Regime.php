<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Regime
{
    public static function all(): array
    {
        return Database::getInstance()
            ->query('SELECT * FROM regime ORDER BY libelle')
            ->fetchAll();
    }
}
