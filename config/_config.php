<?php

// Fichier modèle : copiez-le en "config.php" (dans ce même dossier) et adaptez les valeurs à votre machine.
// config.php n'est pas versionné (voir .gitignore) car il contient les identifiants de la base de données.

// En fonction des routes utilisées, il est possible d'avoir besoin de la session ; on la démarre dans tous les cas.
session_start();

define('TEMPLATE_VIEW_PATH', './views/templates/'); // Le chemin vers les templates de vues.
define('MAIN_VIEW_PATH', TEMPLATE_VIEW_PATH . 'main.php'); // Le chemin vers le template principal.

define('BOOK_IMAGE_PATH', 'uploads/books/'); // Dossier des images des livres.
define('AVATAR_PATH', 'uploads/avatars/'); // Dossier des photos de profil.
define('DEFAULT_BOOK_IMAGE', 'images/default-book.svg'); // Image affichée quand un livre n'a pas de photo.
define('DEFAULT_AVATAR', 'images/default-avatar.svg'); // Image affichée quand un membre n'a pas de photo.

define('DB_HOST', 'localhost');
define('DB_NAME', 'tomtroc');
define('DB_USER', 'root');
define('DB_PASS', '');
