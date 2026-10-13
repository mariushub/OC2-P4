<?php
/**
 * Page de détail d'un livre.
 *
 * Variables envoyées par le contrôleur :
 *      $book Book : le livre à afficher.
 */
?>

<nav class="breadcrumb">
    <div class="container">
        <a href="index.php?action=books">Nos livres</a> &gt; <?= htmlspecialchars($book->getTitle()) ?>
    </div>
</nav>

<section class="book-detail">
    <div class="book-detail-image">
        <img src="<?= htmlspecialchars($book->getImagePath()) ?>" alt="<?= htmlspecialchars($book->getTitle()) ?>">
    </div>

    <div class="book-detail-content">
        <h1><?= htmlspecialchars($book->getTitle()) ?></h1>
        <p class="book-detail-author">par <?= htmlspecialchars($book->getAuthor()) ?></p>

        <hr class="book-detail-separator">

        <h2 class="label">Description</h2>
        <!-- Utils::format() protège déjà le texte (htmlspecialchars) avant d'ajouter les balises <p>. -->
        <div class="book-detail-description"><?= Utils::format($book->getDescription()) ?></div>

        <h2 class="label">Propriétaire</h2>
        <a href="index.php?action=profile&id=<?= $book->getUserId() ?>" class="owner-chip">
            <img src="<?= htmlspecialchars($book->getOwnerAvatarPath()) ?>" alt="" width="48" height="48">
            <span><?= htmlspecialchars($book->getOwnerPseudo()) ?></span>
        </a>

        <?php // On ne propose pas d'écrire à soi-même quand on regarde l'un de ses propres livres. ?>
        <?php if (Utils::getConnectedUserId() !== $book->getUserId()) { ?>
            <a href="index.php?action=messages&to=<?= $book->getUserId() ?>" class="btn btn-primary btn-block book-detail-contact">Envoyer un message</a>
        <?php } ?>
    </div>
</section>
