# Ecoride

***

# Description du projet Ecoride
EcoRide est une application web de covoiturage écologique permettant aux utilisateurs de :

* Rechercher des trajets
* Consulter les détails des covoiturages
* Participer à un trajet
* Gérer leur espace utilisateur
* Et suivre les trajets en cours

L’objectif principal est de promouvoir la mobilité durable à travers un site simple, responsive et agréable à utiliser.

***

## Organisation du projet

La disposition du projet se découpe en 2 dossiers, 
l'un nommé App contient tout le code de l'application.
l'autre nommé doc contient donc toute la documentation du projet tel que :

* Chartes graphique
* Maquettes Wireframe et MockUp
* Diagramme d'utilisation
* Diagramme de classe
* Diagramme de séquence 

***

### Déploiement du site 

Depuis la racine du projet, placez-vous dans `App/` :

1. Démarrer les services locaux (MySQL + Mailpit) : `docker compose up -d database mailer`
2. Installer les dépendances PHP : `composer install`
3. Installer les dépendances front : `npm install` puis build : `npm run build`
4. Lancer Symfony : `symfony serve -d` (ou le serveur PHP interne) puis accéder à l’URL fournie.

Variables à ajuster dans `.env.local` (non versionné) : `DATABASE_URL` (MySQL), `APP_SECRET`, `MAILER_DSN`.

Pour accéder au profil Administrateur :

pseudo: gaetan  
mot de passe : 12345678

Pour l'employé:

Pseudo: jenny1233  
mot de passe : 12345678

Technologie utilisé:

HTML, CSS, Boostrap, PHP, symfony et github
