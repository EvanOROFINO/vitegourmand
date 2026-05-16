<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class AllergeneRepository
{
    public static function all(): array
    {
        return Database::getInstance()
            ->query('SELECT * FROM allergene ORDER BY libelle')
            ->fetchAll();
    }
}
