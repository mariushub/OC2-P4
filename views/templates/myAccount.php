<?php
/**
 * Page "Mon compte" : photo et informations personnelles du membre connecté, et sa bibliothèque.
 *
 * Variables envoyées par le contrôleur :
 *      $user User : le membre connecté.
 *      $books Book[] : les livres de sa bibliothèque.
 *      $errors string[] : les erreurs du formulaire d'informations personnelles.
 *      $pseudo string : le pseudo à afficher dans le formulaire.
 *      $email string : l'email à afficher dans le formulaire.
 */

$booksCount = count($books);
?>

<section class="page-light account-page">
    <div class="container">
        <h1 class="page-title">Mon compte</h1>

        <div class="account-layout">
            <div class="account-card account-summary">
                <!-- La photo est envoyée dès qu'elle est choisie (voir js/script.js). -->
                <form action="index.php?action=updateAvatar" method="post" enctype="multipart/form-data" class="avatar-form">
                    <input type="hidden" name="csrfToken" value="<?= Utils::getCsrfToken() ?>">
                    <img class="profile-avatar" src="<?= htmlspecialchars($user->getAvatarPath()) ?>" alt="Votre photo de profil" width="135" height="135">
                    <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp" class="visually-hidden" data-auto-submit>
                    <label for="avatar" class="avatar-edit">modifier</label>
                    <noscript><button type="submit" class="btn btn-outline">Envoyer la photo</button></noscript>
                </form>

                <hr class="profile-separator">

                <h2 class="profile-pseudo"><?= htmlspecialchars($user->getPseudo()) ?></h2>
                <p class="profile-since">Membre depuis <?= Utils::timeSince($user->getCreatedAt()) ?></p>

                <p class="label">Bibliothèque</p>
                <p class="profile-books-count">
                    <img src="images/icons/books.svg" alt="" width="11" height="14">
                    <?= $booksCount ?> livre<?= $booksCount > 1 ? 's' : '' ?>
                </p>
            </div>

            <div class="account-card account-info">
                <h2>Vos informations personnelles</h2>

                <?php if (!empty($errors)) { ?>
                    <div class="form-errors">
                        <?php foreach ($errors as $error) { ?>
                            <p><?= htmlspecialchars($error) ?></p>
                        <?php } ?>
                    </div>
                <?php } ?>

                <form action="index.php?action=updateAccount" method="post" class="form-filled">
                    <input type="hidden" name="csrfToken" value="<?= Utils::getCsrfToken() ?>">

                    <div class="form-group">
                        <label for="email">Adresse email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <!-- On n'affiche jamais le mot de passe actuel (on ne le connaît pas : seul son hash est en base). -->
                        <input type="password" id="password" name="password" minlength="8" placeholder="Laisser vide pour ne pas le changer" autocomplete="new-password">
                    </div>

                    <div class="form-group">
                        <label for="pseudo">Pseudo</label>
                        <input type="text" id="pseudo" name="pseudo" value="<?= htmlspecialchars($pseudo) ?>" minlength="3" maxlength="50" required>
                    </div>

                    <button type="submit" class="btn btn-outline">Enregistrer</button>
                </form>
            </div>
        </div>

        <div class="account-library-header">
            <h2 class="visually-hidden">Ma bibliothèque</h2>
            <a href="index.php?action=addBookForm" class="btn btn-primary">Ajouter un livre</a>
        </div>

        <div class="library">
            <?php if (empty($books)) { ?>
                <p class="library-empty">Votre bibliothèque est vide : ajoutez votre premier livre !</p>
            <?php } else { ?>
                <table class="library-table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Description</th>
                            <th>Disponibilité</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book) { ?>
                            <tr>
                                <td><img class="library-image" src="<?= htmlspecialchars($book->getImagePath()) ?>" alt="" width="78" height="78"></td>
                                <td><a href="index.php?action=book&id=<?= $book->getId() ?>"><?= htmlspecialchars($book->getTitle()) ?></a></td>
                                <td><?= htmlspecialchars($book->getAuthor()) ?></td>
                                <td class="library-description"><?= htmlspecialchars(Utils::truncate($book->getDescription(), 85)) ?></td>
                                <td>
                                    <?php if ($book->isAvailable()) { ?>
                                        <span class="tag tag-available">disponible</span>
                                    <?php } else { ?>
                                        <span class="tag tag-unavailable">non dispo.</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <div class="library-actions">
                                        <a href="index.php?action=editBookForm&id=<?= $book->getId() ?>">Éditer</a>
                                        <!-- Suppression en POST avec jeton CSRF : un simple lien (GET) pourrait être déclenché à l'insu du membre. -->
                                        <form action="index.php?action=deleteBook" method="post" data-confirm="Supprimer « <?= htmlspecialchars($book->getTitle()) ?> » de votre bibliothèque ?">
                                            <input type="hidden" name="csrfToken" value="<?= Utils::getCsrfToken() ?>">
                                            <input type="hidden" name="id" value="<?= $book->getId() ?>">
                                            <button type="submit" class="link-danger">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </div>
</section>
