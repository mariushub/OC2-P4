<?php
/**
 * Formulaire d'inscription.
 *
 * Variables envoyées par le contrôleur :
 *      $errors string[] : la liste des erreurs à afficher (vide s'il n'y en a pas).
 *      $pseudo string : le pseudo déjà saisi.
 *      $email string : l'email déjà saisi.
 */
?>

<section class="auth">
    <div class="auth-form">
        <h1>Inscription</h1>

        <?php if (!empty($errors)) { ?>
            <div class="form-errors">
                <?php foreach ($errors as $error) { ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php } ?>
            </div>
        <?php } ?>

        <form action="index.php?action=register" method="post">
            <input type="hidden" name="csrfToken" value="<?= Utils::getCsrfToken() ?>">

            <div class="form-group">
                <label for="pseudo">Pseudo</label>
                <input type="text" id="pseudo" name="pseudo" value="<?= htmlspecialchars($pseudo) ?>" minlength="3" maxlength="50" required>
            </div>

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" minlength="8" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
        </form>

        <p class="auth-switch">Déjà inscrit ? <a href="index.php?action=loginForm">Connectez-vous</a></p>
    </div>

    <div class="auth-image">
        <img src="images/auth.jpg" alt="">
    </div>
</section>
