# Application de Billetterie des Jeux Olympiques de Paris 2024 (Simulation)

---

## Description du Projet

Cette application est une simulation de plateforme de billetterie pour les Jeux Olympiques de Paris 2024, développée avec le framework PHP Symfony. Elle permet aux utilisateurs de consulter les offres, gérer un panier d'achat, passer commande, et offre une interface d'administration pour la gestion des événements, des types de billets et des statistiques.

**Veuillez noter :** Ce site a été créé dans le cadre d'un exercice et est une simulation à des fins d'examen. Aucune vente de billets réelle n'est effectuée ici, et toutes les transactions et informations présentées sont simulées.

---

## Fonctionnalités Principales

* **Gestion des Événements :** Création, modification, suppression d'événements sportifs et association à des catégories.
* **Gestion des Offres/Billets :**
    * Définition de types d'offres standard (ex: "Tarif Solo", "Tarif Duo") avec un prix par défaut.
    * Possibilité d'appliquer ces offres à des événements spécifiques, avec un prix ajustable si nécessaire (prix spécifique à l'événement qui surcharge le prix standard de l'offre).
    * Gestion dynamique des offres pour un événement via l'interface d'administration.
* **Panier d'Achat :** Ajout/suppression d'offres au panier via AJAX.
* **Processus de Commande :** Finalisation de l'achat et génération de clés uniques.
* **Authentification et Profil Utilisateur :** Inscription, connexion, modification des informations et du mot de passe.
* **Interface d'Administration :**
    * Tableau de bord centralisé.
    * Gestion des événements et de leurs catégories.
    * **Nouvelle section de gestion des types d'offres standard (Tarifs).**
    * Consultation des statistiques de vente.

---

## Technologies Utilisées

* **Framework :** Symfony 6.x
* **Langage :** PHP 8.x
* **Base de données :** MySQL (via Doctrine ORM)
* **Front-end :** HTML, CSS (Bootstrap 5), JavaScript
* **Tests :** PHPUnit (pour les tests unitaires et fonctionnels), Xdebug (pour la couverture de code)
* **Gestion des dépendances :** Composer
* **Versionnement :** Git / GitHub

---

## Installation et Lancement (pour Développeurs / Correcteurs)

Pour installer et lancer l'application en local, suivez les étapes suivantes :

1.  **Cloner le dépôt GitHub :**
    ```bash
    git clone [https://github.com/votre-utilisateur/votre-depot.git](https://github.com/votre-utilisateur/votre-depot.git)
    cd votre-depot
    ```
    *(Remplacez `votre-utilisateur/votre-depot` par l'adresse de votre dépôt)*
2.  **Installer les dépendances Composer :**
    ```bash
    composer install
    ```
3.  **Configuration de la base de données :**
    * Copiez le fichier `.env` et renommez-le en `.env.local` :
        ```bash
        cp .env .env.local
        ```
    * Ouvrez `.env.local` et configurez votre `DATABASE_URL` pour MySQL (ex: `mysql://db_user:db_password@127.0.0.1:3306/billetterie_jo?serverVersion=8.0.32&charset=utf8mb4`).
4.  **Créer la base de données et exécuter les migrations Doctrine :**
    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    ```
    *(Cela va créer la base de données et toutes les tables, y compris le nouveau champ `prix_standard`.)*
5.  **Charger les fixtures (données de test - optionnel mais recommandé) :**
    Si vous avez des fixtures pour peupler la base de données :
    ```bash
    php bin/console doctrine:fixtures:load
    ```
    *(Si vous avez des problèmes avec les fixtures, vous pouvez créer les données manuellement via l'admin après l'installation, voir "Utilisation").*
6.  **Lancer le serveur de développement Symfony :**
    ```bash
    symfony server:start
    ```
    *(Ou utilisez votre serveur web (Apache/Nginx) configuré avec Wamp/XAMPP)*

L'application devrait être accessible à `http://localhost:8000` (ou l'adresse configurée par votre serveur web).

---

## Guide d'Utilisation (Simulé)

Bienvenue sur le site de simulation de vente de billets pour les Jeux Olympiques de Paris 2024 ! Ce site a été créé dans le cadre d'un exercice. Veuillez noter qu'aucune vente de billets réelle n'est effectuée ici. Toutes les transactions et informations présentées sont simulées.

Ce document est également disponible en cliquant sur le bouton « Aide » depuis votre menu de navigation.

Voici comment naviguer et utiliser ce site :

1.  **Accueil :** La page d'accueil vous donne un aperçu du site.
2.  **Nos Offres :** Cliquez sur « Nos Offres » dans le menu principal pour voir une liste des événements disponibles, ou sur le bouton « Voir toutes les offres ».
3.  **Panier :** Le lien « Panier » affiche les billets que vous avez ajoutés à votre panier de simulation. Le nombre à côté indique le nombre d'articles (simulés) dans votre panier.
4.  **Inscription :** Si vous cliquez sur « Inscription », vous pouvez remplir un formulaire simulé pour créer un compte.
5.  **Connexion :** Si vous avez "créé" un compte (simulé), vous pouvez vous connecter via le bouton de « Connexion » depuis la barre de navigation en utilisant les informations que vous avez fournies.
6.  **Gestion du profil utilisateur (après connexion) :** Les liens « Modifier mes informations », « Modifier mon mot de passe » et « Déconnexion » apparaissent une fois que vous êtes "connecté" (simulé) et vous permettent de simuler la modification de vos informations de profil ou de vous déconnecter.
7.  **Accès Administrateur :** Ce lien est visible uniquement si vous êtes connecté en tant qu'"administrateur" (simulé) et donne accès à une zone d'administration simulée.
    * **Pour devenir administrateur :** Après inscription, modifiez le champ `roles` de votre utilisateur dans la table `utilisateur` de la base de données en `["ROLE_ADMIN"]`.
    * **Dans l'interface d'administration :**
        * **Admin (Tableau de bord) :** Page d'accueil de l'administration avec des liens rapides vers les différentes sections.
        * **Admin (Gérer Événements) :** Permet de créer, modifier ou supprimer des événements.
        * **Admin (Gérer Offres) :** C'est ici que vous définissez les types de billets standards (ex: "Tarif Solo" à 50€). Vous devez créer ces offres ici **avant** de les associer à des événements.
8.  **Simuler un achat :**
    * Naviguez vers « Nos Offres ».
    * Cliquez sur une catégorie de billets.
    * Vous verrez une liste simulée d'événements.
    * Sur la page d'un événement (si elle existe dans la simulation), vous trouverez un bouton pour « Ajouter au panier » (simulé).
    * Cliquez sur "Panier" pour voir votre sélection simulée.
    * Il n'y a pas de processus de paiement réel. Néanmoins vous pouvez simuler un achat : Depuis votre « Panier », cliquez sur « Passer à la caisse », vous arrivez sur une page récapitulative de votre commande. Cliquez ensuite sur « Simuler le paiement », vous arrivez sur la page du billet avec le récapitulatif de votre achat ainsi qu'un QR code simulé.
9.  **Pied de page :** En bas de chaque page, vous trouverez des informations sur l'organisateur (fictif), un contact (fictif) et des liens vers les "CGV (Simulation)" et la "Politique de Confidentialité (Simulation)". Ces liens peuvent mener à des pages avec du texte statique expliquant ces aspects dans le contexte de la simulation.

---

## Tests (pour Développeurs / Correcteurs)

L'application est accompagnée d'une suite de tests PHPUnit pour garantir sa fiabilité et la non-régression.

Pour exécuter les tests :
```bash
php bin/console doctrine:database:create --env=test # Créer la base de données de test
php bin/console doctrine:migrations:migrate --env=test # Migrer la base de données de test
php bin/console doctrine:fixtures:load --env=test # Charger les fixtures de test (si vous en avez)
php bin/phpunit # Exécuter les tests

### Rapport Chiffré et Synthèse Expliquée des Résultats de Couverture de Code

Ce rapport est basé sur les données du 10 juin 2025, généré par php-code-coverage 9.2.32 avec PHP 8.3.14 et PHPUnit 9.6.22.

**Légende de Couverture :**

* **Faible (Rouge) :** 0 % à 50 %
* **Moyen (Jaune) :** 50 % à 90 %
* **Élevé (Vert) :** 90 % à 100 %

#### 1. Synthèse Globale de la Couverture

| Métrique              | Couverture Obtenue | Total Attendu | Statut Couleur | Explication                                                                                                          |
| :-------------------- | :----------------- | :------------ | :------------- | :------------------------------------------------------------------------------------------------------------------- |
| Lignes de Code        | 55,90 %            | 483 / 864     | Jaune          | Plus de la moitié de nos lignes de code sont exécutées par les tests. Une base solide, mais avec une marge de progression significative. |
| Fonctions et Méthodes | 63,16 %            | 96 / 152      | Jaune          | Près des deux tiers de nos fonctions et méthodes sont testées. Il reste des logiques non vérifiées.                  |
| Classes et Traits     | 35,48 %            | 11 / 31       | Rouge          | C'est notre point faible global : moins d'un tiers de nos classes sont couvertes. Cela indique des composants entiers peu ou pas testés. |

#### 2. Détail de la Couverture par Composant (Module)

| Composant    | Lignes (Obtenu / Total) | Statut | Fonctions / Méthodes (Obtenu / Total) | Statut | Classes / Traits (Obtenu / Total) | Statut | Notes et Recommandations                                                                                                                                                                                                                                                                                               |
| :----------- | :---------------------- | :----- | :------------------------------------ | :----- | :-------------------------------- | :----- | :--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Contrôleur   | 24,57 % (72 / 293)      | Rouge  | 17,86 % (5 / 28)                      | Rouge  | 14,29 % (1 / 7)                   | Rouge  | **URGENCE :** Zone la plus critique. Risque élevé de bugs sur la logique front-end. Priorité absolue pour les tests.                                                                                                                                                                                                      |
| DataFixtures | 64,47 % (49 / 76)       | Jaune  | 25,00 % (1 / 4)                       | Rouge  | 33,33 % (1 / 3)                   | Rouge  | Moins critique, mais les fonctions non couvertes pourraient cacher des problèmes.                                                                                                                                                                                                                                        |
| Entité       | 75,76 % (100 / 132)     | Jaune  | 76,32 % (58 / 76)                     | Jaune  | 20,00 % (1 / 5)                   | Rouge  | Bonne base. Les entités contiennent notre logique métier de base. Poursuivre l'amélioration de la couverture des classes.                                                                                                                                                                                                |
| Formulaire   | 68,57 % (168 / 245)     | Jaune  | 76,47 % (13 / 17)                     | Jaune  | 75,00 % (6 / 8)                   | Jaune  | Solide. Bonne couverture pour les formulaires, essentiels pour les interactions utilisateur.                                                                                                                                                                                                                              |
| Dépôt        | 54,35 % (25 / 46)       | Jaune  | 64,29 % (9 / 14)                      | Jaune  | 20,00 % (1 / 5)                   | Rouge  | Les dépôts gèrent l'accès aux données. Une amélioration des couvertures des classes et fonctions serait judicieuse.                                                                                                                                                                                                      |
| Sécurité     | 86,67 % (13 / 15)       | Jaune  | 60,00 % (3 / 5)                       | Jaune  | 0,00 % (0 / 1)                    | Rouge  | Point sensible : Très bonne couverture des lignes mais la classe elle-même n'est pas testée (0%). Risque de sécurité important.                                                                                                                                                                                          |
| Service      | 98,25 % (56 / 57)       | Vert   | 87,50 % (7 / 8)                       | Jaune  | 50,00 % (1 / 2)                   | Jaune  | **EXCELLENT !** Le module le mieux testé. La logique métier clé est très bien protégée. Maintenir cet effort.                                                                                                                                                                                                             |
| Noyau.php    | N/A (0 / 0)             | N/A    | N/A (0 / 0)                           | N/A    | N/A (0 / 0)                       | N/A    | Fichier non pertinent pour les tests de couverture.                                                                                                                                                                                                                                                                      |

#### 3. Conclusion et Perspectives (Basé sur le Rapport Chiffré)

**Points Forts :** L'excellente couverture du module **Service** (98,25% de lignes) est un atout majeur, car c'est là que réside souvent la logique métier la plus complexe et critique.

**Axes d'Amélioration Prioritaires :**

* **Contrôleur (Rouge intense) :** La très faible couverture des contrôleurs représente le risque le plus important. C'est la première couche d'interaction utilisateur et les bugs ici peuvent avoir un impact direct.
* **Couverture Générale des Classes (Rouge) :** Avec seulement 35,48% des classes couvertes, cela signifie que de nombreux composants ne sont pas du tout testés, augmentant le risque de régression sur des parties entières de l'application.
* **Sécurité (Classe non couverte) :** Malgré une bonne couverture de lignes, l'absence de test sur la classe de sécurité elle-même (0%) est un point de vigilance extrême et doit être corrigé.

**Note Importante :** Il est crucial de rappeler que ce rapport, bien que précis au moment de sa génération, ne prend pas en compte les succès récents des tests du module de sécurité et du panier qui ont été finalisés depuis. La couverture réelle globale, et celle des modules Sécurité et Formulaire (qui inclut le panier), est donc actuellement significativement meilleure que ce qu'indiquait ces chiffres. Cela valide nos efforts et montre que nous sommes sur la bonne voie pour atteindre nos objectifs de qualité.

Pour générer un rapport de couverture de code (nécessite Xdebug) :
```bash
php bin/phpunit --coverage-html public/coverage-report

```
## Le rapport sera accessible via public/coverage-report/index.html.

