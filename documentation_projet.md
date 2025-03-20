# Documentation du Projet Système de Tickets

## Table des matières
1. [Introduction](#introduction)
2. [Prérequis](#prérequis)
3. [Installation](#installation)
4. [Structure du Projet](#structure-du-projet)
5. [Configuration](#configuration)
6. [Fonctionnalités](#fonctionnalités)
7. [Sécurité](#sécurité)
8. [Utilisation](#utilisation)

## Introduction
Ce projet est un système de gestion de tickets développé avec Symfony 6. Il permet aux clients de créer des tickets de support et aux administrateurs de les gérer. Le système est conçu pour être simple d'utilisation tout en offrant des fonctionnalités avancées de gestion.

## Prérequis
- PHP 8.2 ou supérieur
- Composer
- MySQL/MariaDB
- WAMP (Windows Apache MySQL PHP)
- Symfony CLI

## Installation

### 1. Création du projet
```bash
composer create-project symfony/skeleton:"6.4.*" ticket-system
cd ticket-system
```

### 2. Installation des dépendances
```bash
composer require symfony/orm-pack
composer require symfony/maker-bundle --dev
composer require symfony/form
composer require symfony/security-bundle
composer require symfony/validator
composer require symfony/twig-bundle
composer require doctrine/doctrine-fixtures-bundle --dev
```

### 3. Configuration de la base de données
Dans le fichier `.env` :
```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/ticket_system?serverVersion=8.0.32&charset=utf8mb4"
```

### 4. Création de la base de données
```bash
php bin/console doctrine:database:create
```

## Structure du Projet

### Entités
1. **Admin**
   - Gestion des utilisateurs
   - Rôles : ROLE_ADMIN et ROLE_USER

2. **Ticket**
   - ID
   - Email de l'auteur
   - Date de création
   - Date de fermeture
   - Description
   - Catégorie
   - Statut
   - Responsable

3. **Category**
   - ID
   - Nom
   - Liste des tickets associés

4. **Status**
   - ID
   - Nom
   - Liste des tickets associés

5. **Responsible**
   - ID
   - Nom
   - Email
   - Liste des tickets associés

### Contrôleurs
1. **SecurityController**
   - Gestion de l'authentification
   - Routes : /login, /logout

2. **TicketController**
   - Gestion des tickets
   - Routes : /, /ticket/new, /ticket/list, /ticket/{id}, /ticket/{id}/edit, /ticket/{id}/close

### Templates
- base.html.twig : Template de base avec Bootstrap
- security/login.html.twig : Page de connexion
- ticket/*.twig : Templates pour la gestion des tickets

## Configuration

### Sécurité (security.yaml)
```yaml
security:
    password_hashers:
        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'
    
    providers:
        app_user_provider:
            entity:
                class: App\Entity\Admin
                property: email

    firewalls:
        main:
            lazy: true
            provider: app_user_provider
            form_login:
                login_path: app_login
                check_path: app_login
                enable_csrf: true
            logout:
                path: app_logout

    access_control:
        - { path: ^/login, roles: PUBLIC_ACCESS }
        - { path: ^/ticket/new, roles: PUBLIC_ACCESS }
        - { path: ^/ticket/list, roles: ROLE_USER }
        - { path: ^/ticket/\d+, roles: ROLE_USER }
        - { path: ^/ticket/\d+/edit, roles: ROLE_ADMIN }
        - { path: ^/ticket/\d+/close, roles: ROLE_ADMIN }
```

## Fonctionnalités

### Pour les visiteurs
- Création de tickets
- Accès à la page d'accueil

### Pour les utilisateurs connectés
- Visualisation de la liste des tickets
- Visualisation des détails d'un ticket

### Pour les administrateurs
- Création de tickets avec tous les champs
- Modification des tickets
- Fermeture des tickets
- Gestion complète du système

## Sécurité
- Authentification par email/mot de passe
- Protection CSRF
- Gestion des rôles
- Validation des données
- Protection des routes

## Utilisation

### Comptes de test
1. **Administrateur**
   - Email : admin@example.com
   - Mot de passe : admin123

2. **Utilisateur standard**
   - Email : user@example.com
   - Mot de passe : user123

### Lancement du serveur
```bash
symfony server:start -d
```
L'application est accessible à l'adresse : http://localhost:8000

### Création des données de test
```bash
php bin/console doctrine:fixtures:load
```

## Validation des données
- Description : 20-250 caractères
- Email : format valide
- Catégorie : obligatoire
- Statut : obligatoire pour les administrateurs

## Interface utilisateur
- Design responsive avec Bootstrap 5
- Navigation intuitive
- Messages flash pour les notifications
- Formulaires avec validation
- Tableaux de données organisés 