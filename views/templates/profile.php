<?php
/**
 * Profil public d'un membre, avec sa bibliothèque.
 *
 * Variables envoyées par le contrôleur :
 *      $user User : le membre affiché.
 *      $books Book[] : les livres de sa bibliothèque.
 */

$booksCount = count($books);
?>

<section class="page-light profile-page">
    <div class="container profile-layout">
        <div class="profile-card">
            <img class="profile-avatar" src="<?= htmlspecialchars($user->getAvatarPath()) ?>" alt="Photo de profil de <?= htmlspecialchars($user->getPseudo()) ?>" width="135" height="135">

            <hr class="profile-separator">

            <h1 class="profile-pseudo"><?= htmlspecialchars($user->getPseudo()) ?></h1>
            <p class="profile-since">Membre depuis <?= Utils::timeSince($user->getCreatedAt()) ?></p>

            <p class="label">Bibliothèque</p>
            <p class="profile-books-count">
                <img src="images/icons/books.svg" alt="" width="11" height="14">
                <?= $booksCount ?> livre<?= $booksCount > 1 ? 's' : '' ?>
            </p>

            <?php if (Utils::getConnectedUserId() !== $user->getId()) { ?>
                <a href="index.php?action=messages&to=<?= $user->getId() ?>" class="btn btn-outline">Écrire un message</a>
            <?php } ?>
        </div>

        <div class="library">
            <?php if (empty($books)) { ?>
                <p class="library-empty">Ce membre n'a pas encore ajouté de livre.</p>
            <?php } else { ?>
                <table class="library-table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book) { ?>
                            <tr>
                                <td><img class="library-image" src="<?= htmlspecialchars($book->getImagePath()) ?>" alt="" width="78" height="78"></td>
                                <td><a href="index.php?action=book&id=<?= $book->getId() ?>"><?= htmlspecialchars($book->getTitle()) ?></a></td>
                                <td><?= htmlspecialchars($book->getAuthor()) ?></td>
                                <td class="library-description"><?= htmlspecialchars(Utils::truncate($book->getDescription(), 85)) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </div>
</section>
