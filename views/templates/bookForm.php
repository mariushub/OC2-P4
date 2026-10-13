<?php
/**
 * Formulaire d'ajout ou de modification d'un livre (le même formulaire sert aux deux).
 *
 * Variables envoyées par le contrôleur :
 *      $book Book : le livre à modifier, ou un livre vide (id = -1) pour un ajout.
 *      $errors string[] : les erreurs à afficher (vide s'il n'y en a pas).
 */

$isNewBook = $book->getId() === -1;
?>

<section class="page-light book-form-page">
    <div class="container">
        <a href="index.php?action=myAccount" class="back-link">&larr; retour</a>
        <h1 class="page-title"><?= $isNewBook ? 'Ajouter un livre' : 'Modifier les informations' ?></h1>

        <!-- enctype="multipart/form-data" est obligatoire pour pouvoir envoyer un fichier. -->
        <form action="index.php?action=saveBook" method="post" enctype="multipart/form-data" class="book-form form-filled">
            <input type="hidden" name="csrfToken" value="<?= Utils::getCsrfToken() ?>">
            <input type="hidden" name="id" value="<?= $book->getId() ?>">

            <div class="book-form-photo">
                <p class="book-form-label">Photo</p>
                <img id="book-preview" class="book-form-preview" src="<?= htmlspecialchars($book->getImagePath()) ?>" alt="Photo du livre" width="488" height="488">
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" class="visually-hidden" data-preview="book-preview">
                <label for="image" class="book-form-photo-link"><?= $isNewBook ? 'Ajouter une photo' : 'Modifier la photo' ?></label>
            </div>

            <div class="book-form-fields">
                <?php if (!empty($errors)) { ?>
                    <div class="form-errors">
                        <?php foreach ($errors as $error) { ?>
                            <p><?= htmlspecialchars($error) ?></p>
                        <?php } ?>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <label for="title">Titre</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($book->getTitle()) ?>" maxlength="255" required>
                </div>

                <div class="form-group">
                    <label for="author">Auteur</label>
                    <input type="text" id="author" name="author" value="<?= htmlspecialchars($book->getAuthor()) ?>" maxlength="255" required>
                </div>

                <div class="form-group">
                    <label for="description">Commentaire</label>
                    <textarea id="description" name="description" rows="14"><?= htmlspecialchars($book->getDescription()) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="isAvailable">Disponibilité</label>
                    <select id="isAvailable" name="isAvailable">
                        <option value="1"<?= $book->isAvailable() ? ' selected' : '' ?>>disponible</option>
                        <option value="0"<?= $book->isAvailable() ? '' : ' selected' ?>>non dispo.</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary book-form-submit">Valider</button>
            </div>
        </form>
    </div>
</section>
