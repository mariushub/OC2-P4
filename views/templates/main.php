<?php
/**
 * Template principal : il "contient" ce qui a été généré par les autres vues.
 * Le header et le footer sont donc écrits une seule fois, ici, pour toutes les pages.
 *
 * Les variables qui doivent impérativement être définies sont :
 *      $title string : le titre de la page.
 *      $content string : le contenu de la page.
 *      $flashes array : les messages de confirmation ou d'erreur à afficher.
 *      $unreadCount int : le nombre de messages non lus du membre connecté.
 */

// L'action en cours permet de mettre en gras le lien du menu correspondant à la page affichée.
$currentAction = Utils::request('action', 'home');
$isActive = fn(array $actions) => in_array($currentAction, $actions, true) ? 'active' : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Tom Troc</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:wght@400;600&display=swap">
    <link rel="stylesheet" href="./css/style.css">
    <script src="./js/script.js" defer></script>
</head>

<body>
    <header class="header">
        <div class="container header-inner">
            <a href="index.php" class="header-logo">
                <img src="images/logo.svg" alt="Tom Troc - Accueil" width="155" height="51">
            </a>

            <nav class="nav" aria-label="Menu principal">
                <div class="nav-main">
                    <a href="index.php?action=home" class="<?= $isActive(['home']) ?>">Accueil</a>
                    <a href="index.php?action=books" class="<?= $isActive(['books', 'book']) ?>">Nos livres à l'échange</a>
                </div>

                <div class="nav-user">
                    <a href="index.php?action=messages" class="<?= $isActive(['messages']) ?>">
                        <img src="images/icons/messagerie.svg" alt="" width="15" height="14">
                        Messagerie
                        <?php if ($unreadCount > 0) { ?>
                            <span class="badge"><?= $unreadCount ?><span class="visually-hidden"> non lu<?= $unreadCount > 1 ? 's' : '' ?></span></span>
                        <?php } ?>
                    </a>
                    <a href="index.php?action=myAccount" class="<?= $isActive(['myAccount', 'addBookForm', 'editBookForm', 'saveBook']) ?>">
                        <img src="images/icons/compte.svg" alt="" width="10" height="14">
                        Mon compte
                    </a>
                    <?php if (Utils::isConnected()) { ?>
                        <a href="index.php?action=logout">Déconnexion</a>
                    <?php } else { ?>
                        <a href="index.php?action=loginForm" class="<?= $isActive(['loginForm', 'login', 'registerForm', 'register']) ?>">Connexion</a>
                    <?php } ?>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <?php foreach ($flashes as $flash) { ?>
            <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>" role="status"><?= htmlspecialchars($flash['message']) ?></div>
        <?php } ?>

        <?= $content /* Ici est affiché le contenu réel de la page. */ ?>
    </main>

    <footer class="footer">
        <a href="index.php?action=privacy">Politique de confidentialité</a>
        <a href="index.php?action=legal">Mentions légales</a>
        <span>Tom Troc©</span>
        <img src="images/logo-footer.svg" alt="" width="22" height="18">
    </footer>
</body>
</html>
