<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class RegimeRepository
{
    public static function all(): array
    {
        return Database::getInstance()
            ->query('SELECT * FROM regime ORDER BY libelle')
            ->fetchAll();
    }
}
