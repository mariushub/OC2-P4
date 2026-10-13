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
