<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Mailer;
use App\Models\ContactMessage;

final class ContactController extends Controller
{
    public function show(): void
    {
        $this->view('home/contact', ['pageTitle' => 'Contact']);
    }

    public function send(): void
    {
        $this->verifyCsrf();
        $titre       = $this->input('titre');
        $description = $this->input('description');
        $email       = $this->input('email');

        if (empty($titre) || empty($description) || empty($email)) {
            $this->flash('error', 'Tous les champs sont obligatoires.');
            $this->redirect('/contact');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Adresse e-mail invalide.');
            $this->redirect('/contact');
        }

        ContactMessage::create((string) $titre, (string) $description, (string) $email);

        $config = require __DIR__ . '/../../config/config.php';
        Mailer::send(
            $config['mail']['contact_to'],
            'Nouveau message de contact : ' . $titre,
            '<h1>Nouveau message reçu</h1>
             <p><strong>De :</strong> ' . e((string) $email) . '</p>
             <p><strong>Sujet :</strong> ' . e((string) $titre) . '</p>
             <hr>
             <p>' . nl2br(e((string) $description)) . '</p>'
        );

        $this->flash('success', 'Votre message a été envoyé. Nous vous répondrons rapidement.');
        $this->redirect('/contact');
    }
}
