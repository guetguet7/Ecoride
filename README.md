# 🚗 EcoRide — Plateforme de covoiturage écologique

EcoRide est une application web développée avec **Symfony 7** visant à promouvoir la **mobilité durable** via le covoiturage.  
La plateforme permet aux utilisateurs de proposer ou réserver des trajets, de gérer leurs profils, et offre un espace d’administration pour le suivi de l’activité.

Ce projet a été réalisé dans le cadre de l’**ECF – Développeur Web / Web Mobile (Bac +2)**.

---

## Objectif du projet

EcoRide vise à :

Réduire l’impact environnemental des déplacements

Favoriser le covoiturage local

Proposer une plateforme moderne, accessible et sécurisée

---

## 📁 Structure du dépôt

Le dépôt EcoRide est organisé comme suit :

- `EcoRide/` : application Symfony complète (toutes les commandes doivent être lancées depuis ce dossier)
- `docs/` : documentation du projet (diagrammes UML, MCD/MLD, wireframes, dump SQL)
- `README.md` : documentation d’installation et de présentation du projet

---

## ⚙️ Prérequis

Avant de lancer le projet en local, assure-toi de disposer des éléments suivants :

- PHP **≥ 8.2**
- Extensions PHP : `ctype`, `iconv`, `pdo`, `pdo_mysql`
- Composer
- MySQL ou MariaDB
- Symfony CLI (facultatif mais recommandé)
- Serveur SMTP de développement (Mailpit ou Mailhog recommandé)

---

## 🚀 Installation du projet

```bash
# Cloner le dépôt
git clone git@github.com:VotreUtilisateur/EcoRide.git
cd EcoRide/EcoRide

---

## Configuration de l’environnement

# Copier le fichier d’environnement
cp .env.example .env

Configurer ensuite les variables suivantes dans .env ou .env.local :

DATABASE_URL

MAILER_DSN

APP_ENV=dev

---

## Installation des dépendances PHP
composer install

---

## Base de données
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction

---

##Import du jeu de données (optionnel)

Un dump SQL est disponible dans le dossier docs/ :

mysql -u <utilisateur> -p <nom_base> < ../docs/dump_ecoride.sql

---

## Assets (CSS / JavaScript)

EcoRide utilise AssetMapper, Stimulus et ImportMap.

Après une première installation ou une modification des assets :

php bin/console importmap:install
php bin/console asset-map:compile

---

## Lancer l’application
Avec Symfony CLI
symfony server:start


L’application sera accessible à l’adresse :
-> http://127.0.0.1:8000

Lancement sans barre de debug (HTML propre)
APP_ENV=prod APP_DEBUG=0 symfony server:start

---

## Gestion des emails (développement)

EcoRide dispose d’un formulaire de contact et de notifications email.

Mailpit (recommandé)
mailpit


Interface accessible sur :
-> http://127.0.0.1:8025

Configurer le DSN dans .env :

MAILER_DSN=smtp://localhost:1025

---

## Rôles utilisateurs

L’application gère plusieurs profils :

Visiteur : consultation des trajets publics

Utilisateur : réservation de trajets, gestion du profil

Chauffeur : création et gestion de trajets

Administrateur : gestion globale de la plateforme

---

##Arborescence principale
Racine du dépôt
.
├─ EcoRide/
├─ docs/
└─ README.md

Application Symfony (EcoRide/)
.
├─ assets/
│  ├─ controllers/        # Contrôleurs Stimulus
│  ├─ js/                 # Modules JavaScript
│  ├─ styles/             # Feuilles de style
├─ bin/
├─ config/
├─ migrations/
├─ public/
│  ├─ uploads/
│  └─ images/
├─ src/
│  ├─ Controller/
│  ├─ Entity/
│  ├─ Repository/
│  └─ Service/
├─ templates/
├─ translations/
├─ var/
├─ vendor/
├─ composer.json
├─ composer.lock
└─ importmap.php

---

## Documentation complémentaire

Le dossier docs/ contient :

Diagrammes UML (cas d’utilisation, classes)

MCD / MLD

Wireframes web & mobile

Dump SQL

Dossier professionnel (ECF)