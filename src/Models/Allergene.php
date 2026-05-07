<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Allergene
{
    public static function all(): array
    {
        return Database::getInstance()
            ->query('SELECT * FROM allergene ORDER BY libelle')
            ->fetchAll();
    }
}
