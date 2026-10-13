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

    /**
     * Affiche le formulaire d'ajout d'un livre.
     * @return void
     */
    public function showAddBookForm(): void
    {
        Utils::checkIfUserIsConnected();

        $view = new View('Ajouter un livre');
        $view->render('bookForm', ['book' => new Book(), 'errors' => []]);
    }

    /**
     * Affiche le formulaire de modification d'un livre.
     * @return void
     */
    public function showEditBookForm(): void
    {
        $userId = Utils::checkIfUserIsConnected();
        $book = $this->getOwnedBook((int) Utils::request('id', -1), $userId);

        $view = new View('Modifier les informations');
        $view->render('bookForm', ['book' => $book, 'errors' => []]);
    }

    /**
     * Traite le formulaire d'ajout ou de modification d'un livre.
     * Un livre sans id (-1) est un nouveau livre, sinon c'est une modification.
     * @return void
     */
    public function saveBook(): void
    {
        $userId = Utils::checkIfUserIsConnected();
        Utils::checkCsrfToken();

        $id = (int) Utils::request('id', -1);
        if ($id === -1) {
            $book = new Book(['user_id' => $userId]);
        } else {
            $book = $this->getOwnedBook($id, $userId);
        }

        $book->setTitle(trim(Utils::request('title', '')));
        $book->setAuthor(trim(Utils::request('author', '')));
        $book->setDescription(trim(Utils::request('description', '')));
        $book->setIsAvailable(Utils::request('isAvailable', '1') === '1');

        $errors = [];
        if ($book->getTitle() === '' || $book->getAuthor() === '') {
            $errors[] = "Le titre et l'auteur sont obligatoires.";
        }
        if (mb_strlen($book->getTitle()) > 255 || mb_strlen($book->getAuthor()) > 255) {
            $errors[] = "Le titre et l'auteur ne doivent pas dépasser 255 caractères.";
        }

        // L'image n'est envoyée que si le reste du formulaire est valide (sinon le fichier serait inutile).
        if (empty($errors)) {
            try {
                $image = ImageUploader::upload('image', BOOK_IMAGE_PATH);
                if ($image !== null) {
                    $book->setImage($image);
                }
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        // En cas d'erreur, on réaffiche le formulaire avec ce que le membre a saisi.
        if (!empty($errors)) {
            $view = new View($id === -1 ? 'Ajouter un livre' : 'Modifier les informations');
            $view->render('bookForm', ['book' => $book, 'errors' => $errors]);
            return;
        }

        $bookManager = new BookManager();
        if ($id === -1) {
            $bookManager->addBook($book);
            Utils::addFlash('Le livre a été ajouté à votre bibliothèque.');
        } else {
            $bookManager->updateBook($book);
            Utils::addFlash('Le livre a été modifié.');
        }
        Utils::redirect('myAccount');
    }

    /**
     * Supprime un livre de la bibliothèque du membre connecté.
     * @return void
     */
    public function deleteBook(): void
    {
        $userId = Utils::checkIfUserIsConnected();
        Utils::checkCsrfToken();

        $book = $this->getOwnedBook((int) Utils::request('id', -1), $userId);

        $bookManager = new BookManager();
        $bookManager->deleteBook($book->getId());

        Utils::addFlash('Le livre « ' . $book->getTitle() . ' » a été supprimé.');
        Utils::redirect('myAccount');
    }

    /**
     * Récupère un livre en vérifiant qu'il appartient bien au membre connecté.
     * Sans cette vérification, n'importe quel membre pourrait modifier ou supprimer le livre d'un autre
     * simplement en changeant l'id dans l'URL ou dans le formulaire.
     * @param int $bookId
     * @param int $userId : le membre connecté.
     * @throws Exception : 404 si le livre n'existe pas, 403 s'il appartient à quelqu'un d'autre.
     * @return Book
     */
    private function getOwnedBook(int $bookId, int $userId): Book
    {
        $bookManager = new BookManager();
        $book = $bookManager->getBookById($bookId);

        if ($book === null) {
            throw new Exception("Ce livre n'existe pas.", 404);
        }
        if ($book->getUserId() !== $userId) {
            throw new Exception("Vous ne pouvez modifier que les livres de votre bibliothèque.", 403);
        }
        return $book;
    }
}
