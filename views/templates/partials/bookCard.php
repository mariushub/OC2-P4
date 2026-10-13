<?php
/**
 * Carte d'un livre (réutilisée sur l'accueil et sur la page "Nos livres à l'échange").
 * À inclure avec require, dans une boucle qui définit la variable :
 *      $book Book : le livre à afficher.
 */
?>
<a href="index.php?action=book&id=<?= $book->getId() ?>" class="book-card">
    <div class="book-card-image">
        <img src="<?= htmlspecialchars($book->getImagePath()) ?>" alt="<?= htmlspecialchars($book->getTitle()) ?>">
        <?php if (!$book->isAvailable()) { ?>
            <span class="tag tag-unavailable">non dispo.</span>
        <?php } ?>
    </div>
    <div class="book-card-info">
        <h3><?= htmlspecialchars($book->getTitle()) ?></h3>
        <p class="book-card-author"><?= htmlspecialchars($book->getAuthor()) ?></p>
        <p class="book-card-owner">Vendu par : <?= htmlspecialchars($book->getOwnerPseudo()) ?></p>
    </div>
</a>
