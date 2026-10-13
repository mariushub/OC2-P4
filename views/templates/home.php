<?php
/**
 * Page d'accueil.
 *
 * Variables envoyées par le contrôleur :
 *      $books Book[] : les derniers livres ajoutés.
 */
?>

<section class="hero">
    <div class="container hero-inner">
        <div class="hero-text">
            <h1>Rejoignez nos lecteurs passionnés</h1>
            <p>Donnez une nouvelle vie à vos livres en les échangeant avec d'autres amoureux de la lecture. Nous croyons en la magie du partage de connaissances et d'histoires à travers les livres.</p>
            <a href="index.php?action=books" class="btn btn-primary">Découvrir</a>
        </div>
        <figure class="hero-image">
            <img src="images/hero.jpg" alt="Un libraire lisant au milieu de piles de livres" width="404" height="539">
            <figcaption>Hamza</figcaption>
        </figure>
    </div>
</section>

<section class="last-books">
    <div class="container">
        <h2 class="section-title">Les derniers livres ajoutés</h2>

        <div class="book-grid">
            <?php foreach ($books as $book) {
                require TEMPLATE_VIEW_PATH . 'partials/bookCard.php';
            } ?>
        </div>

        <a href="index.php?action=books" class="btn btn-primary">Voir tous les livres</a>
    </div>
</section>

<section class="how-it-works">
    <div class="container">
        <h2 class="section-title">Comment ça marche ?</h2>
        <p class="section-intro">Échanger des livres avec TomTroc c'est simple et amusant ! Suivez ces étapes pour commencer :</p>

        <ol class="steps">
            <li>Inscrivez-vous gratuitement sur notre plateforme.</li>
            <li>Ajoutez les livres que vous souhaitez échanger à votre profil.</li>
            <li>Parcourez les livres disponibles chez d'autres membres.</li>
            <li>Proposez un échange et discutez avec d'autres passionnés de lecture.</li>
        </ol>

        <a href="index.php?action=books" class="btn btn-outline">Voir tous les livres</a>
    </div>
</section>

<img class="banner" src="images/banner.jpg" alt="" width="1440" height="230">

<section class="values">
    <div class="values-inner">
        <h2 class="section-title">Nos valeurs</h2>
        <p>Chez Tom Troc, nous mettons l'accent sur le partage, la découverte et la communauté. Nos valeurs sont ancrées dans notre passion pour les livres et notre désir de créer des liens entre les lecteurs. Nous croyons en la puissance des histoires pour rassembler les gens et inspirer des conversations enrichissantes.</p>
        <p>Notre association a été fondée avec une conviction profonde : chaque livre mérite d'être lu et partagé.</p>
        <p>Nous sommes passionnés par la création d'une plateforme conviviale qui permet aux lecteurs de se connecter, de partager leurs découvertes littéraires et d'échanger des livres qui attendent patiemment sur les étagères.</p>

        <div class="values-signature">
            <span>L'équipe Tom Troc</span>
            <img src="images/heart.png" alt="" width="132" height="114">
        </div>
    </div>
</section>
