<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Theme
{
    public static function all(): array
    {
        return Database::getInstance()
            ->query('SELECT * FROM theme ORDER BY libelle')
            ->fetchAll();
    }
}
