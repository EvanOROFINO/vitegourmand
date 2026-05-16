-- =====================================================================
-- Vite & Gourmand — Données de test (seed)
-- Tous les comptes démo ont pour mot de passe : Password123!
-- (hash bcrypt PHP password_hash, coût 10)
-- =====================================================================

USE vitegourmand;

-- Force le charset client sur utf8mb4 pour préserver les accents
-- même quand le seed est importé depuis un terminal Windows / phpMyAdmin
-- dont la connexion par défaut serait en latin1.
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET CHARACTER SET utf8mb4;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE plat_allergene;
TRUNCATE TABLE menu_plat;
TRUNCATE TABLE menu_image;
TRUNCATE TABLE commande_statut_historique;
TRUNCATE TABLE avis;
TRUNCATE TABLE commande;
TRUNCATE TABLE menu;
TRUNCATE TABLE plat;
TRUNCATE TABLE allergene;
TRUNCATE TABLE theme;
TRUNCATE TABLE regime;
TRUNCATE TABLE possede;
TRUNCATE TABLE utilisateur;
TRUNCATE TABLE role;
TRUNCATE TABLE horaire;
TRUNCATE TABLE contact_message;
SET FOREIGN_KEY_CHECKS = 1;

-- ----- Rôles -----
INSERT INTO role (role_id, libelle) VALUES
    (1, 'utilisateur'),
    (2, 'employe'),
    (3, 'administrateur');

-- ----- Utilisateurs (mdp démo : Password123!) -----
INSERT INTO utilisateur (utilisateur_id, email, password, prenom, nom, telephone, adresse_postale, ville, pays, actif) VALUES
    (1, 'admin@vitegourmand.fr',   '$2y$10$ymxGpD436p.j3lZpniO93O2ALDAN2w5IfMJztJQf9VxreIh480OXC', 'Julie',  'Dupont',   '0612345678', '12 rue Sainte-Catherine', 'Bordeaux', 'France', TRUE),
    (2, 'employe@vitegourmand.fr', '$2y$10$ymxGpD436p.j3lZpniO93O2ALDAN2w5IfMJztJQf9VxreIh480OXC', 'José',   'Martinez', '0623456789', '34 cours Pasteur',         'Bordeaux', 'France', TRUE),
    (3, 'user@vitegourmand.fr',    '$2y$10$ymxGpD436p.j3lZpniO93O2ALDAN2w5IfMJztJQf9VxreIh480OXC', 'Marie',  'Lambert',  '0634567890', '56 avenue Thiers',         'Bordeaux', 'France', TRUE),
    (4, 'jean@example.fr',         '$2y$10$ymxGpD436p.j3lZpniO93O2ALDAN2w5IfMJztJQf9VxreIh480OXC', 'Jean',   'Bernard',  '0645678901', '78 rue Judaïque',          'Bordeaux', 'France', TRUE);

INSERT INTO possede (utilisateur_id, role_id) VALUES
    (1, 3),
    (1, 2),
    (1, 1),
    (2, 2),
    (2, 1),
    (3, 1),
    (4, 1);

-- ----- Thèmes -----
INSERT INTO theme (theme_id, libelle) VALUES
    (1, 'Classique'),
    (2, 'Noël'),
    (3, 'Pâques'),
    (4, 'Évènement'),
    (5, 'Été');

-- ----- Régimes -----
INSERT INTO regime (regime_id, libelle) VALUES
    (1, 'Classique'),
    (2, 'Végétarien'),
    (3, 'Végan'),
    (4, 'Sans gluten'),
    (5, 'Halal');

-- ----- Allergènes -----
INSERT INTO allergene (allergene_id, libelle) VALUES
    (1, 'Gluten'),
    (2, 'Lactose'),
    (3, 'Œufs'),
    (4, 'Fruits à coque'),
    (5, 'Crustacés'),
    (6, 'Poisson'),
    (7, 'Soja'),
    (8, 'Arachide');

-- ----- Plats -----
INSERT INTO plat (plat_id, titre, photo, type) VALUES
    (1,  'Velouté de potiron au cumin',           NULL, 'entree'),
    (2,  'Foie gras maison et son chutney',       NULL, 'entree'),
    (3,  'Tartare de saumon à l''aneth',          NULL, 'entree'),
    (4,  'Salade de chèvre chaud aux noix',       NULL, 'entree'),
    (5,  'Carpaccio de bœuf',                     NULL, 'entree'),
    (6,  'Magret de canard sauce miel',           NULL, 'plat'),
    (7,  'Pavé de bœuf et gratin dauphinois',     NULL, 'plat'),
    (8,  'Filet mignon en croûte',                NULL, 'plat'),
    (9,  'Risotto aux champignons',               NULL, 'plat'),
    (10, 'Curry de légumes au lait de coco',      NULL, 'plat'),
    (11, 'Pavé de cabillaud aux agrumes',         NULL, 'plat'),
    (12, 'Tiramisu aux fruits rouges',            NULL, 'dessert'),
    (13, 'Bûche au chocolat et marrons',          NULL, 'dessert'),
    (14, 'Tarte Tatin maison',                    NULL, 'dessert'),
    (15, 'Salade de fruits frais',                NULL, 'dessert'),
    (16, 'Mousse au chocolat noir',               NULL, 'dessert');

-- ----- Allergènes des plats -----
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES
    (2, 1), (2, 2),
    (3, 6),
    (4, 2), (4, 4),
    (7, 1), (7, 2),
    (8, 1), (8, 2), (8, 3),
    (9, 2),
    (11, 6),
    (12, 1), (12, 2), (12, 3),
    (13, 1), (13, 2), (13, 3), (13, 4),
    (14, 1), (14, 2), (14, 3),
    (16, 2), (16, 3);

-- ----- Menus -----
INSERT INTO menu (menu_id, titre, description, nombre_personne_minimum, prix_par_personne, conditions_menu, quantite_restante, theme_id, regime_id, actif) VALUES
    (1, 'Menu Tradition Bordelaise',
        'Un repas raffiné aux saveurs du Sud-Ouest, mettant en valeur les produits locaux et le savoir-faire de Julie.',
        6, 45.00,
        'Commande à passer au moins 5 jours avant la prestation. Conservation au frais conseillée 24h avant la dégustation.',
        10, 1, 1, TRUE),

    (2, 'Menu de Noël Festif',
        'Un menu d''exception pour les fêtes : foie gras, magret, bûche maison. Idéal pour 6 à 20 convives.',
        6, 65.00,
        'Commande à passer au moins 10 jours avant la prestation. Disponible uniquement entre le 15 décembre et le 5 janvier.',
        8, 2, 1, TRUE),

    (3, 'Menu Végétarien Gourmand',
        'Un menu 100% végétarien et créatif, sans concession sur la qualité ni le goût.',
        4, 38.00,
        'Commande à passer 3 jours minimum avant la prestation.',
        15, 1, 2, TRUE),

    (4, 'Menu Pâques Familial',
        'Un menu généreux pour célébrer Pâques en famille autour d''un agneau pascal et de douceurs chocolatées.',
        6, 52.00,
        'Disponible entre mars et avril. Commande 7 jours avant.',
        5, 3, 1, TRUE),

    (5, 'Menu Été Frais',
        'Un menu léger et estival, parfait pour les réceptions en extérieur.',
        8, 35.00,
        'Conservation au frais impérative. Commande 4 jours avant.',
        12, 5, 1, TRUE),

    (6, 'Menu Évènement Prestige',
        'Le menu signature de Vite & Gourmand pour vos grands évènements : mariage, anniversaire, réception professionnelle.',
        20, 85.00,
        'Commande 14 jours minimum avant la prestation. Devis personnalisé sur demande pour > 50 convives.',
        3, 4, 1, TRUE);

-- ----- Composition des menus (menu_plat) -----
INSERT INTO menu_plat (menu_id, plat_id) VALUES
    -- Menu 1 Tradition Bordelaise
    (1, 2), (1, 7), (1, 14),
    -- Menu 2 Noël Festif
    (2, 2), (2, 6), (2, 13),
    -- Menu 3 Végétarien Gourmand
    (3, 1), (3, 9), (3, 15), (3, 4),
    -- Menu 4 Pâques Familial
    (4, 4), (4, 8), (4, 16),
    -- Menu 5 Été Frais
    (5, 3), (5, 11), (5, 15),
    -- Menu 6 Évènement Prestige
    (6, 5), (6, 8), (6, 12), (6, 13);

-- ----- Galerie d'images (placeholders Unsplash) -----
INSERT INTO menu_image (menu_id, chemin, alt_texte, ordre) VALUES
    (1, 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800', 'Table de tradition bordelaise', 0),
    (2, 'https://images.unsplash.com/photo-1482275548304-a58859dc31b7?w=800', 'Table de fête de Noël',          0),
    (3, 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800', 'Plat végétarien coloré',         0),
    (4, 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=800', 'Repas familial de Pâques',       0),
    (5, 'https://images.unsplash.com/photo-1505253758473-96b7015fcd40?w=800', 'Buffet d''été frais',            0),
    (6, 'https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?w=800', 'Table de réception prestige',    0);

-- ----- Horaires -----
INSERT INTO horaire (jour, heure_ouverture, heure_fermeture, ordre) VALUES
    ('Lundi',    '09:00', '18:00', 1),
    ('Mardi',    '09:00', '18:00', 2),
    ('Mercredi', '09:00', '18:00', 3),
    ('Jeudi',    '09:00', '20:00', 4),
    ('Vendredi', '09:00', '20:00', 5),
    ('Samedi',   '10:00', '17:00', 6),
    ('Dimanche', 'Fermé', 'Fermé', 7);

-- ----- Commandes de démo -----
INSERT INTO commande (numero_commande, utilisateur_id, menu_id, date_commande, date_prestation, heure_livraison, adresse_livraison, ville_livraison, distance_km, nombre_personne, prix_menu, prix_livraison, reduction, prix_total, statut, pret_materiel, restitution_materiel) VALUES
    ('CMD-20260301-DEMO01', 3, 1, '2026-03-01 12:00:00', '2026-03-15', '12:00:00', '56 avenue Thiers',  'Bordeaux',  0,    8,  360.00, 0,    36.00, 324.00, 'terminee',     FALSE, FALSE),
    ('CMD-20260410-DEMO02', 3, 3, '2026-04-10 10:00:00', '2026-04-25', '13:30:00', '56 avenue Thiers',  'Bordeaux',  0,    6,  228.00, 0,    0,     228.00, 'accepte',      FALSE, FALSE),
    ('CMD-20260420-DEMO03', 4, 5, '2026-04-20 14:00:00', '2026-05-10', '19:00:00', '12 rue de la Paix', 'Mérignac',  10,   10, 350.00, 10.90,35.00, 325.90, 'en_preparation',FALSE, FALSE);

-- ----- Historique de statuts pour la commande terminée -----
INSERT INTO commande_statut_historique (numero_commande, statut, date_changement) VALUES
    ('CMD-20260301-DEMO01', 'en_attente',            '2026-03-01 12:00:00'),
    ('CMD-20260301-DEMO01', 'accepte',               '2026-03-02 09:30:00'),
    ('CMD-20260301-DEMO01', 'en_preparation',        '2026-03-15 08:00:00'),
    ('CMD-20260301-DEMO01', 'en_cours_de_livraison', '2026-03-15 11:00:00'),
    ('CMD-20260301-DEMO01', 'livre',                 '2026-03-15 12:15:00'),
    ('CMD-20260301-DEMO01', 'terminee',              '2026-03-15 12:30:00'),
    ('CMD-20260410-DEMO02', 'en_attente',            '2026-04-10 10:00:00'),
    ('CMD-20260410-DEMO02', 'accepte',               '2026-04-11 14:00:00'),
    ('CMD-20260420-DEMO03', 'en_attente',            '2026-04-20 14:00:00'),
    ('CMD-20260420-DEMO03', 'accepte',               '2026-04-21 09:00:00'),
    ('CMD-20260420-DEMO03', 'en_preparation',        '2026-05-10 06:00:00');

-- ----- Avis -----
INSERT INTO avis (utilisateur_id, numero_commande, note, description, statut) VALUES
    (3, 'CMD-20260301-DEMO01', 5, 'Repas absolument délicieux, service au top ! Julie et José sont des artistes en cuisine. À recommander sans hésiter.', 'valide');
