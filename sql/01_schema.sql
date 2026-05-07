-- =====================================================================
-- Vite & Gourmand — Schéma de base de données relationnelle
-- ECF 2 TP DWWM Studi — Evan OROFINO
-- =====================================================================

DROP DATABASE IF EXISTS vitegourmand;
CREATE DATABASE vitegourmand
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE vitegourmand;

-- ---------------------------------------------------------------------
-- Table : role (utilisateur, employe, administrateur)
-- ---------------------------------------------------------------------
CREATE TABLE role (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : utilisateur (clients + employés + admin)
-- ---------------------------------------------------------------------
CREATE TABLE utilisateur (
    utilisateur_id   INT AUTO_INCREMENT PRIMARY KEY,
    email            VARCHAR(180) NOT NULL UNIQUE,
    password         VARCHAR(255) NOT NULL,
    prenom           VARCHAR(80)  NOT NULL,
    nom              VARCHAR(80)  NOT NULL,
    telephone        VARCHAR(20)  NOT NULL,
    adresse_postale  VARCHAR(255) NOT NULL,
    ville            VARCHAR(80)  NOT NULL DEFAULT 'Bordeaux',
    pays             VARCHAR(80)  NOT NULL DEFAULT 'France',
    actif            BOOLEAN      NOT NULL DEFAULT TRUE,
    reset_token      VARCHAR(64)  DEFAULT NULL,
    reset_expires    DATETIME     DEFAULT NULL,
    created_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : possede (relation utilisateur ↔ role, n,n)
-- ---------------------------------------------------------------------
CREATE TABLE possede (
    utilisateur_id INT NOT NULL,
    role_id        INT NOT NULL,
    PRIMARY KEY (utilisateur_id, role_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id) ON DELETE CASCADE,
    FOREIGN KEY (role_id)        REFERENCES role(role_id)               ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : theme (Noël, Pâques, classique, événement, etc.)
-- ---------------------------------------------------------------------
CREATE TABLE theme (
    theme_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle  VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : regime (classique, vegetarien, vegan, sans gluten, etc.)
-- ---------------------------------------------------------------------
CREATE TABLE regime (
    regime_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle   VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : menu
-- ---------------------------------------------------------------------
CREATE TABLE menu (
    menu_id                 INT AUTO_INCREMENT PRIMARY KEY,
    titre                   VARCHAR(150)   NOT NULL,
    description             TEXT           NOT NULL,
    nombre_personne_minimum INT            NOT NULL,
    prix_par_personne       DECIMAL(10, 2) NOT NULL,
    conditions_menu         TEXT           DEFAULT NULL,
    quantite_restante       INT            NOT NULL DEFAULT 0,
    theme_id                INT            NOT NULL,
    regime_id               INT            NOT NULL,
    actif                   BOOLEAN        NOT NULL DEFAULT TRUE,
    created_at              TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (theme_id)  REFERENCES theme(theme_id),
    FOREIGN KEY (regime_id) REFERENCES regime(regime_id),
    INDEX idx_theme (theme_id),
    INDEX idx_regime (regime_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : menu_image (galerie d'images d'un menu)
-- ---------------------------------------------------------------------
CREATE TABLE menu_image (
    image_id   INT AUTO_INCREMENT PRIMARY KEY,
    menu_id    INT          NOT NULL,
    chemin     VARCHAR(255) NOT NULL,
    alt_texte  VARCHAR(255) DEFAULT NULL,
    ordre      INT          NOT NULL DEFAULT 0,
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : plat (entrée / plat / dessert)
-- ---------------------------------------------------------------------
CREATE TABLE plat (
    plat_id INT AUTO_INCREMENT PRIMARY KEY,
    titre   VARCHAR(150) NOT NULL,
    photo   VARCHAR(255) DEFAULT NULL,
    type    ENUM('entree', 'plat', 'dessert') NOT NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : menu_plat (un plat peut être présent dans plusieurs menus)
-- ---------------------------------------------------------------------
CREATE TABLE menu_plat (
    menu_id INT NOT NULL,
    plat_id INT NOT NULL,
    PRIMARY KEY (menu_id, plat_id),
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id) ON DELETE CASCADE,
    FOREIGN KEY (plat_id) REFERENCES plat(plat_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : allergene (gluten, lactose, fruits à coque, etc.)
-- ---------------------------------------------------------------------
CREATE TABLE allergene (
    allergene_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle      VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : plat_allergene (un plat possède une liste d'allergènes)
-- ---------------------------------------------------------------------
CREATE TABLE plat_allergene (
    plat_id      INT NOT NULL,
    allergene_id INT NOT NULL,
    PRIMARY KEY (plat_id, allergene_id),
    FOREIGN KEY (plat_id)      REFERENCES plat(plat_id)            ON DELETE CASCADE,
    FOREIGN KEY (allergene_id) REFERENCES allergene(allergene_id)  ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : commande
-- ---------------------------------------------------------------------
CREATE TABLE commande (
    numero_commande      VARCHAR(50)  PRIMARY KEY,
    utilisateur_id       INT          NOT NULL,
    menu_id              INT          NOT NULL,
    date_commande        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_prestation      DATE         NOT NULL,
    heure_livraison      TIME         NOT NULL,
    adresse_livraison    VARCHAR(255) NOT NULL,
    ville_livraison      VARCHAR(80)  NOT NULL,
    distance_km          DECIMAL(6,2) NOT NULL DEFAULT 0,
    nombre_personne      INT          NOT NULL,
    prix_menu            DECIMAL(10,2) NOT NULL,
    prix_livraison       DECIMAL(10,2) NOT NULL DEFAULT 0,
    reduction            DECIMAL(10,2) NOT NULL DEFAULT 0,
    prix_total           DECIMAL(10,2) NOT NULL,
    statut               VARCHAR(50)  NOT NULL DEFAULT 'en_attente',
    pret_materiel        BOOLEAN      NOT NULL DEFAULT FALSE,
    restitution_materiel BOOLEAN      NOT NULL DEFAULT FALSE,
    motif_annulation     TEXT         DEFAULT NULL,
    mode_contact         VARCHAR(50)  DEFAULT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id),
    FOREIGN KEY (menu_id)        REFERENCES menu(menu_id),
    INDEX idx_statut (statut),
    INDEX idx_user (utilisateur_id),
    INDEX idx_menu (menu_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : commande_statut_historique (suivi des changements de statut)
-- ---------------------------------------------------------------------
CREATE TABLE commande_statut_historique (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    numero_commande VARCHAR(50)  NOT NULL,
    statut          VARCHAR(50)  NOT NULL,
    commentaire     TEXT         DEFAULT NULL,
    date_changement DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (numero_commande) REFERENCES commande(numero_commande) ON DELETE CASCADE,
    INDEX idx_commande (numero_commande)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : avis (laissé par un utilisateur après une commande terminée)
-- ---------------------------------------------------------------------
CREATE TABLE avis (
    avis_id          INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id   INT          NOT NULL,
    numero_commande  VARCHAR(50)  NOT NULL,
    note             TINYINT      NOT NULL,
    description      TEXT         NOT NULL,
    statut           ENUM('en_attente', 'valide', 'refuse') NOT NULL DEFAULT 'en_attente',
    created_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id)  REFERENCES utilisateur(utilisateur_id),
    FOREIGN KEY (numero_commande) REFERENCES commande(numero_commande),
    UNIQUE KEY unique_avis_par_commande (numero_commande)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : horaire (jours d'ouverture)
-- ---------------------------------------------------------------------
CREATE TABLE horaire (
    horaire_id      INT AUTO_INCREMENT PRIMARY KEY,
    jour            VARCHAR(20) NOT NULL UNIQUE,
    heure_ouverture VARCHAR(10) NOT NULL,
    heure_fermeture VARCHAR(10) NOT NULL,
    ordre           INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : contact_message (formulaire de contact)
-- ---------------------------------------------------------------------
CREATE TABLE contact_message (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    titre       VARCHAR(150) NOT NULL,
    description TEXT         NOT NULL,
    email       VARCHAR(180) NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
