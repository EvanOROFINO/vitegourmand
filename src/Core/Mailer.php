<?php
/**
 * Service d'envoi de mail. En mode développement (APP_DEBUG=true), les mails
 * sont écrits dans /var/mail-debug/ au lieu d'être envoyés réellement.
 */

declare(strict_types=1);

namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use Throwable;

final class Mailer
{
    public static function send(string $to, string $subject, string $body): bool
    {
        $config = require __DIR__ . '/../../config/config.php';

        if (($config['app']['debug'] ?? false) || empty($config['mail']['username'])) {
            return self::logMail($to, $subject, $body);
        }

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $config['mail']['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['mail']['username'];
            $mail->Password   = $config['mail']['password'];
            $mail->Port       = $config['mail']['port'];
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($config['mail']['from'], $config['mail']['from_name']);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            return $mail->send();
        } catch (Throwable $e) {
            error_log('Erreur envoi mail : ' . $e->getMessage());
            return self::logMail($to, $subject, $body);
        }
    }

    private static function logMail(string $to, string $subject, string $body): bool
    {
        $dir = __DIR__ . '/../../var/mail-debug';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $filename = $dir . '/' . date('Ymd_His') . '_' . preg_replace('/[^a-z0-9]/i', '_', $to) . '.html';
        $content  = "<!-- À : $to -->\n<!-- Sujet : $subject -->\n<!-- Date : " . date('c') . " -->\n\n$body";

        return (bool) file_put_contents($filename, $content);
    }
}
