<?php

/**
 * Contrôleur de la page d'accueil.
 */
class HomeController
{
    /**
     * Affiche la page d'accueil avec les 4 derniers livres ajoutés.
     * @return void
     */
    public function showHome(): void
    {
        // 1. Le contrôleur demande les données au modèle...
        $bookManager = new BookManager();
        $books = $bookManager->getLastBooks(4);

        // 2. ... puis il les transmet à la vue, qui s'occupe uniquement de l'affichage.
        $view = new View('Accueil');
        $view->render('home', ['books' => $books]);
    }
}
