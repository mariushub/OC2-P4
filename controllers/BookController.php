<?php

/**
 * Contrôleur des livres : liste des livres à l'échange, détail d'un livre,
 * et gestion de sa bibliothèque (ajout, modification, suppression).
 */
class BookController
{
    /**
     * Affiche la page "Nos livres à l'échange", avec la recherche par titre.
     * @return void
     */
    public function showBooks(): void
    {
        $search = trim(Utils::request('search', ''));

        $bookManager = new BookManager();
        $books = $bookManager->getBooks($search);

        $view = new View("Nos livres à l'échange");
        $view->render('books', ['books' => $books, 'search' => $search]);
    }

    /**
     * Affiche le détail d'un livre.
     * @return void
     */
    public function showBook(): void
    {
        // (int) transforme ce qui vient de l'URL en entier : "abc" devient 0, qui ne correspond à aucun livre.
        $id = (int) Utils::request('id', -1);

        $bookManager = new BookManager();
        $book = $bookManager->getBookById($id);

        if ($book === null) {
            throw new Exception("Ce livre n'existe pas.", 404);
        }

        $view = new View($book->getTitle());
        $view->render('book', ['book' => $book]);
    }
}
