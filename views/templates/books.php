<?php
/**
 * Page "Nos livres à l'échange" : tous les livres, avec une recherche par titre.
 *
 * Variables envoyées par le contrôleur :
 *      $books Book[] : les livres à afficher.
 *      $search string : le texte recherché (chaîne vide s'il n'y a pas de recherche).
 */
?>

<section class="page-light">
    <div class="books-page">
        <div class="books-header">
            <h1>Nos livres à l'échange</h1>

            <!-- Formulaire en GET : la recherche apparaît dans l'URL, on peut donc la partager ou la mettre en favori. -->
            <form class="search-form" action="index.php" method="get" role="search">
                <input type="hidden" name="action" value="books">
                <label for="search" class="visually-hidden">Rechercher un livre</label>
                <input type="search" id="search" name="search" placeholder="Rechercher un livre" value="<?= htmlspecialchars($search) ?>">
            </form>
        </div>

        <?php if (empty($books)) { ?>
            <p class="books-empty">
                <?php if ($search !== '') { ?>
                    Aucun livre ne correspond à « <?= htmlspecialchars($search) ?> ».
                    <a href="index.php?action=books">Voir tous les livres</a>
                <?php } else { ?>
                    Aucun livre n'est encore proposé à l'échange.
                <?php } ?>
            </p>
        <?php } else { ?>
            <h2 class="visually-hidden">Liste des livres</h2>
            <div class="book-grid">
                <?php foreach ($books as $book) {
                    require TEMPLATE_VIEW_PATH . 'partials/bookCard.php';
                } ?>
            </div>
        <?php } ?>
    </div>
</section>
