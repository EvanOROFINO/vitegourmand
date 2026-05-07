<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class ContactMessage
{
    public static function create(string $titre, string $description, string $email): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('INSERT INTO contact_message (titre, description, email) VALUES (:t, :d, :e)');
        return $stmt->execute([':t' => $titre, ':d' => $description, ':e' => $email]);
    }
}
