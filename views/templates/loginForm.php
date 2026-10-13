<?php
/**
 * Formulaire de connexion.
 *
 * Variables envoyées par le contrôleur :
 *      $error string|null : le message d'erreur à afficher (null s'il n'y en a pas).
 *      $email string : l'email déjà saisi (pour ne pas avoir à le retaper en cas d'erreur).
 */
?>

<section class="auth">
    <div class="auth-form">
        <h1>Connexion</h1>

        <?php if ($error !== null) { ?>
            <div class="form-errors">
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php } ?>

        <form action="index.php?action=login" method="post">
            <input type="hidden" name="csrfToken" value="<?= Utils::getCsrfToken() ?>">

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
        </form>

        <p class="auth-switch">Pas de compte ? <a href="index.php?action=registerForm">Inscrivez-vous</a></p>
    </div>

    <div class="auth-image">
        <img src="images/auth.jpg" alt="">
    </div>
</section>
