-- Script MySQL 8 pour MySQL Workbench
-- Schema coherent avec les entites Symfony (donnees de demonstration incluses)

DROP DATABASE IF EXISTS `ecoride_demo`;
CREATE DATABASE `ecoride_demo` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `ecoride_demo`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Tables principales
--

CREATE TABLE `user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `pseudo` VARCHAR(180) NOT NULL,
  `roles` JSON NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `create_at` DATETIME NOT NULL,
  `update_at` DATETIME DEFAULT NULL,
  `last_login` DATETIME DEFAULT NULL,
  `email` VARCHAR(255) NOT NULL,
  `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `credits` INT NOT NULL DEFAULT 0,
  `rating` DOUBLE PRECISION NOT NULL DEFAULT 0,
  `role_type` VARCHAR(20) NOT NULL DEFAULT 'passenger',
  `is_suspended` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_USER_PSEUDO_EMAIL` (`pseudo`,`email`)
) ENGINE=InnoDB;

CREATE TABLE `user_profile` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) DEFAULT NULL,
  `prenom` VARCHAR(100) DEFAULT NULL,
  `telephone` VARCHAR(20) DEFAULT NULL,
  `adresse` VARCHAR(255) DEFAULT NULL,
  `date_naissance` DATETIME DEFAULT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `user_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_PROFILE_USER` (`user_id`),
  CONSTRAINT `FK_PROFILE_USER` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `voiture` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `modele` VARCHAR(255) NOT NULL,
  `immatriculation` VARCHAR(255) NOT NULL,
  `energie` VARCHAR(255) NOT NULL,
  `couleur` VARCHAR(255) NOT NULL,
  `date_premiere_immatriculation` VARCHAR(255) NOT NULL,
  `user_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_VOITURE_USER` (`user_id`),
  CONSTRAINT `FK_VOITURE_USER` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `rides` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `pseudo` VARCHAR(255) NOT NULL,
  `photo` LONGBLOB NOT NULL,
  `nbplace` VARCHAR(255) NOT NULL,
  `prix` INT NOT NULL DEFAULT 0,
  `date_heure_depart` DATETIME NOT NULL,
  `date_heure_arrivee` DATETIME NOT NULL,
  `lieu_depart` VARCHAR(255) NOT NULL,
  `lieu_arrivee` VARCHAR(255) NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'active',
  `user_id` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_RIDES_USER` (`user_id`),
  CONSTRAINT `FK_RIDES_USER` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE `participation` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `ride_id` INT NOT NULL,
  `amount` INT NOT NULL DEFAULT 0,
  `status` VARCHAR(20) NOT NULL DEFAULT 'confirmed',
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_USER_RIDE` (`user_id`,`ride_id`),
  KEY `IDX_PARTICIPATION_RIDE` (`ride_id`),
  CONSTRAINT `FK_PARTICIPATION_USER` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_PARTICIPATION_RIDE` FOREIGN KEY (`ride_id`) REFERENCES `rides` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `avis` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `commentaire` VARCHAR(255) DEFAULT NULL,
  `note` SMALLINT NOT NULL DEFAULT 0,
  `statut` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `created_at` DATETIME NOT NULL,
  `author_id` INT NOT NULL,
  `driver_id` INT NOT NULL,
  `ride_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AVIS_AUTHOR` (`author_id`),
  KEY `IDX_AVIS_DRIVER` (`driver_id`),
  KEY `IDX_AVIS_RIDE` (`ride_id`),
  CONSTRAINT `FK_AVIS_AUTHOR` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_AVIS_DRIVER` FOREIGN KEY (`driver_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_AVIS_RIDE` FOREIGN KEY (`ride_id`) REFERENCES `rides` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `reset_password_request` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `selector` VARCHAR(20) NOT NULL,
  `hashed_token` VARCHAR(100) NOT NULL,
  `requested_at` DATETIME NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `user_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_RESET_SELECTOR` (`selector`),
  KEY `IDX_RESET_USER` (`user_id`),
  CONSTRAINT `FK_RESET_USER` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `covoiturage` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `date_depart` DATE NOT NULL,
  `heure_depart` DATE NOT NULL,
  `lieu_depart` VARCHAR(255) NOT NULL,
  `date_arrivee` DATE NOT NULL,
  `heure_arrivee` VARCHAR(255) NOT NULL,
  `lieu_arrivee` VARCHAR(255) NOT NULL,
  `statut` VARCHAR(255) NOT NULL,
  `nb_place` INT NOT NULL,
  `prix_personne` DOUBLE PRECISION NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `parametre` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `propriete` VARCHAR(255) NOT NULL,
  `valeur` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `marque` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `libelle` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `role` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `libelle` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `configuration` (
  `id` INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `messenger_messages` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `body` LONGTEXT NOT NULL,
  `headers` LONGTEXT NOT NULL,
  `queue_name` VARCHAR(190) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `available_at` DATETIME NOT NULL,
  `delivered_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_QUEUE_NAME` (`queue_name`),
  KEY `IDX_AVAILABLE_AT` (`available_at`),
  KEY `IDX_DELIVERED_AT` (`delivered_at`)
) ENGINE=InnoDB;

--
-- Jeu de donnees de demonstration
--

INSERT INTO `user` (`pseudo`, `roles`, `password`, `create_at`, `email`, `is_verified`, `credits`, `rating`, `role_type`, `is_suspended`)
VALUES
('driver01', JSON_ARRAY('ROLE_USER', 'ROLE_DRIVER'), '$2y$13$demoDriverHash', '2025-01-10 09:00:00', 'driver01@ecoride.test', 1, 80, 4.7, 'driver', 0),
('passenger01', JSON_ARRAY('ROLE_USER'), '$2y$13$demoPassengerHash', '2025-01-11 10:15:00', 'passenger01@ecoride.test', 1, 35, 4.3, 'passenger', 0);

INSERT INTO `user_profile` (`nom`, `prenom`, `telephone`, `adresse`, `date_naissance`, `photo`, `user_id`) VALUES
('Durand', 'Alice', '+33601020304', '12 rue des Lilas, Lyon', '1995-07-14 00:00:00', NULL, 1),
('Martin', 'Louis', '+33605060708', '4 avenue de Paris, Bordeaux', '1992-05-22 00:00:00', NULL, 2);

INSERT INTO `marque` (`libelle`) VALUES ('Tesla'), ('Renault');

INSERT INTO `voiture` (`modele`, `immatriculation`, `energie`, `couleur`, `date_premiere_immatriculation`, `user_id`) VALUES
('Model 3', 'AB-123-CD', 'Electrique', 'Bleu nuit', '2022-03-01', 1),
('Zoe E-Tech', 'EF-456-GH', 'Electrique', 'Blanc', '2023-06-12', 1);

INSERT INTO `rides` (`pseudo`, `photo`, `nbplace`, `prix`, `date_heure_depart`, `date_heure_arrivee`, `lieu_depart`, `lieu_arrivee`, `status`, `user_id`) VALUES
('driver01', 0x89504E470D0A1A0A, '3', 18, '2025-02-05 08:00:00', '2025-02-05 09:30:00', 'Lyon Part-Dieu', 'Saint-Etienne Chateaucreux', 'active', 1);

INSERT INTO `participation` (`user_id`, `ride_id`, `amount`, `status`, `created_at`) VALUES
(2, 1, 18, 'confirmed', '2025-02-01 12:00:00');

INSERT INTO `avis` (`commentaire`, `note`, `statut`, `created_at`, `author_id`, `driver_id`, `ride_id`) VALUES
('Trajet parfait, conducteur ponctuel.', 5, 'published', '2025-02-05 10:00:00', 2, 1, 1);

INSERT INTO `parametre` (`propriete`, `valeur`) VALUES
('commission_plateforme', '10%'),
('seuil_suspension', '3');

INSERT INTO `role` (`libelle`) VALUES ('ROLE_USER'), ('ROLE_ADMIN'), ('ROLE_DRIVER');

INSERT INTO `reset_password_request` (`selector`, `hashed_token`, `requested_at`, `expires_at`, `user_id`) VALUES
('RST1234567890', 'demoHashedTokenValue', '2025-02-01 08:00:00', '2025-02-02 08:00:00', 2);

COMMIT;
