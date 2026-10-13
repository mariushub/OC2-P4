# TomTroc

Site de mise en relation entre lecteurs pour échanger des livres, réalisé pour l'association TomTroc.
Projet développé en PHP orienté objet avec une architecture MVC, en HTML et CSS, sans aucune librairie tierce.

## Fonctionnalités

- Inscription, connexion et déconnexion des membres
- Page « Mon compte » : modification du profil (pseudo, email, mot de passe, photo) et gestion de sa bibliothèque (ajout, modification, suppression de livres avec photo)
- Profil public de chaque membre avec sa bibliothèque
- Page « Nos livres à l'échange » avec recherche par titre
- Page de détail d'un livre, avec lien vers le profil du propriétaire et vers la messagerie
- Messagerie : liste des conversations, fil de discussion, envoi de messages, compteur de messages non lus dans le menu

## Installation

Prérequis : un serveur local avec PHP 8.2 et MySQL ou MariaDB (par exemple XAMPP), avec l'extension PHP `fileinfo` activée (c'est le cas par défaut avec XAMPP).

1. Cloner le projet dans le dossier de votre serveur local (ex : `C:\xampp\htdocs\p4`).
2. Importer le fichier `sql/tomtroc.sql` (dans phpMyAdmin : onglet *Importer*). Il crée la base `tomtroc`, les tables et des données de test.
3. Copier `config/_config.php` en `config/config.php` et adapter les identifiants de la base de données si nécessaire.
4. Ouvrir http://localhost/p4 dans le navigateur.

Le fichier `sql/tomtroc.sql` peut être réimporté à tout moment pour revenir aux données de test.

## Comptes de test

Tous les comptes ont le mot de passe `password123`.
Compte conseillé pour tester : `nathalie@mail.com` (pseudo *nathalire*), qui a des livres et des conversations, dont un message non lu.

## Base de données

```
user (id, pseudo, email, password, avatar, created_at)
  │ 1
  ├──── 0..N  book (id, #user_id, title, author, image, description, is_available, created_at, updated_at)
  ├──── 0..N  message (id, #sender_id, #receiver_id, content, is_read, created_at)   ← en tant qu'expéditeur
  └──── 0..N  message                                                               ← en tant que destinataire
```

Les clés étrangères (`ON DELETE CASCADE`) garantissent l'intégrité des données : un livre ou un message ne peut pas exister sans membre valide.

## Structure du code

```
index.php          Routeur : point d'entrée unique, appelle le bon contrôleur selon ?action=
config/            Configuration (constantes, BDD) et autoload des classes
controllers/       Contrôleurs : récupèrent les données via les managers et choisissent la vue
models/            Entités (Book, User, Message) et managers (requêtes SQL)
views/             Classe View et templates HTML (views/templates/, header et footer dans main.php)
services/          Classes utilitaires (Utils, ImageUploader)
css/               Feuille de style
js/                Petits scripts en JavaScript natif (aperçu d'image, confirmation de suppression...)
images/            Images du site (logo, photos de mise en page, icônes, images par défaut)
uploads/           Images envoyées par les membres (couvertures de livres, avatars)
sql/               Script de création de la base et données de test
```

## Sécurité

- **Mots de passe** : hashés avec `password_hash()` et vérifiés avec `password_verify()`, jamais stockés en clair.
- **Injections SQL** : toutes les données venant de l'utilisateur passent par des requêtes préparées PDO.
- **Failles XSS** : tout texte affiché est échappé avec `htmlspecialchars()`.
- **Failles CSRF** : chaque formulaire POST contient un jeton secret vérifié côté serveur ; les actions qui modifient des données n'acceptent que des requêtes POST.
- **Contrôle d'accès** : les pages réservées redirigent vers la connexion, et un membre ne peut modifier ou supprimer que ses propres livres (vérification côté serveur).
- **Envoi d'images** : taille limitée à 2 Mo, type vérifié dans le contenu du fichier (JPG, PNG, WEBP), nom de fichier aléatoire, exécution de scripts interdite dans `uploads/` (fichier `.htaccess`).
- **Sessions** : identifiant de session régénéré à la connexion.
- **Erreurs** : les pages introuvables renvoient un code 404 ; les erreurs techniques affichent un message générique (le détail est écrit dans le journal d'erreurs de PHP).
- **Identifiants de la base** : `config/config.php` n'est pas versionné (voir `.gitignore`).

Projet réalisé avec PHP 8.2 et MariaDB 10.4.
