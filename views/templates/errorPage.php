<?php
/**
 * Template pour afficher une page d'erreur.
 *
 * Variables envoyées par le routeur :
 *      $errorMessage string : le message de l'erreur.
 *      $statusCode int : le code HTTP de l'erreur (404, 403 ou 500).
 */
?>

<section class="page-light error-page">
    <p class="error-code"><?= $statusCode ?></p>
    <h1>Oups !</h1>
    <p><?= htmlspecialchars($errorMessage) ?></p>
    <a href="index.php?action=home" class="btn btn-primary">Retour à l'accueil</a>
</section>
