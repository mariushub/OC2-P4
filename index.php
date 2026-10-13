<?php

require_once 'config/config.php';
require_once 'config/autoload.php';

// index.php est le routeur : c'est le seul fichier appelé par le navigateur.
// On récupère l'action demandée dans l'URL (index.php?action=...).
// Si aucune action n'est demandée, on affiche la page d'accueil.
$action = Utils::request('action', 'home');

// Try catch global pour gérer les erreurs.
try {
    // Pour chaque action, on appelle le bon contrôleur et la bonne méthode.
    switch ($action) {
        // Pages accessibles à tous.
        case 'home':
            $homeController = new HomeController();
            $homeController->showHome();
            break;

        case 'books':
            $bookController = new BookController();
            $bookController->showBooks();
            break;

        case 'book':
            $bookController = new BookController();
            $bookController->showBook();
            break;

        case 'profile':
            $userController = new UserController();
            $userController->showProfile();
            break;

        case 'legal':
            $homeController = new HomeController();
            $homeController->showLegal();
            break;

        case 'privacy':
            $homeController = new HomeController();
            $homeController->showPrivacy();
            break;

        // Inscription, connexion et déconnexion.
        case 'registerForm':
            $userController = new UserController();
            $userController->showRegisterForm();
            break;

        case 'register':
            $userController = new UserController();
            $userController->register();
            break;

        case 'loginForm':
            $userController = new UserController();
            $userController->showLoginForm();
            break;

        case 'login':
            $userController = new UserController();
            $userController->login();
            break;

        case 'logout':
            $userController = new UserController();
            $userController->logout();
            break;

        // Pages réservées aux membres connectés (la vérification est faite dans chaque contrôleur).
        case 'myAccount':
            $userController = new UserController();
            $userController->showMyAccount();
            break;

        case 'updateAccount':
            $userController = new UserController();
            $userController->updateAccount();
            break;

        case 'updateAvatar':
            $userController = new UserController();
            $userController->updateAvatar();
            break;

        case 'addBookForm':
            $bookController = new BookController();
            $bookController->showAddBookForm();
            break;

        case 'editBookForm':
            $bookController = new BookController();
            $bookController->showEditBookForm();
            break;

        case 'saveBook':
            $bookController = new BookController();
            $bookController->saveBook();
            break;

        case 'deleteBook':
            $bookController = new BookController();
            $bookController->deleteBook();
            break;

        case 'messages':
            $messageController = new MessageController();
            $messageController->showMessages();
            break;

        case 'sendMessage':
            $messageController = new MessageController();
            $messageController->sendMessage();
            break;

        default:
            throw new Exception("La page demandée n'existe pas.", 404);
    }
} catch (Throwable $e) {
    // Throwable attrape aussi les erreurs PHP (TypeError...), pas seulement les exceptions :
    // le visiteur voit toujours une page d'erreur propre, jamais un message d'erreur PHP brut.
    // Le code de l'exception sert de code HTTP : 404 (page introuvable) ou 403 (accès interdit).
    $statusCode = in_array($e->getCode(), [403, 404], true) ? $e->getCode() : 500;
    http_response_code($statusCode);

    if ($statusCode === 500) {
        // Erreur technique (base de données injoignable, bug...) : on ne montre pas le détail au visiteur,
        // il pourrait révéler des informations sur le serveur. On l'écrit dans le journal d'erreurs de PHP.
        error_log($e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ')');
        $message = 'Une erreur est survenue, veuillez réessayer plus tard.';
    } else {
        $message = $e->getMessage();
    }

    $errorView = new View('Erreur');
    $errorView->render('errorPage', ['errorMessage' => $message, 'statusCode' => $statusCode]);
}
