<?php

/**
 * Classe qui gère les livres : toutes les requêtes SQL sur la table book sont ici.
 */
class BookManager extends AbstractEntityManager
{
    // Début de requête commun à toutes les méthodes : un livre est toujours affiché avec le pseudo
    // et l'avatar de son propriétaire, qu'on récupère grâce à une jointure avec la table user.
    private const SELECT_WITH_OWNER = "SELECT book.*, user.pseudo AS owner_pseudo, user.avatar AS owner_avatar
                                       FROM book
                                       INNER JOIN user ON user.id = book.user_id";

    /**
     * Récupère les derniers livres ajoutés.
     * @param int $limit : le nombre de livres à récupérer.
     * @return array : un tableau d'objets Book.
     */
    public function getLastBooks(int $limit): array
    {
        // $limit est forcément un entier grâce au typage "int" : on peut l'écrire directement dans la requête.
        // Avec un paramètre de requête préparée, PDO l'entourerait de guillemets (LIMIT '4'), ce que MySQL refuse.
        $sql = self::SELECT_WITH_OWNER . " ORDER BY book.created_at DESC LIMIT $limit";
        return $this->createBooks($this->db->query($sql));
    }

    /**
     * Récupère tous les livres, éventuellement filtrés par titre.
     * @param string $search : le texte recherché dans le titre (chaîne vide = tous les livres).
     * @return array : un tableau d'objets Book.
     */
    public function getBooks(string $search = ''): array
    {
        // LIKE '%texte%' : le titre contient le texte, n'importe où.
        // Avec une recherche vide, LIKE '%%' renvoie tous les livres : pas besoin de deux requêtes différentes.
        // La collation utf8mb4_unicode_ci de la table rend la recherche insensible à la casse et aux accents.
        $sql = self::SELECT_WITH_OWNER . " WHERE book.title LIKE :search ORDER BY book.created_at DESC";
        return $this->createBooks($this->db->query($sql, ['search' => '%' . $search . '%']));
    }

    /**
     * Récupère un livre par son id.
     * @param int $id
     * @return Book|null : le livre, ou null s'il n'existe pas.
     */
    public function getBookById(int $id): ?Book
    {
        $sql = self::SELECT_WITH_OWNER . " WHERE book.id = :id";
        $book = $this->db->query($sql, ['id' => $id])->fetch();
        return $book ? new Book($book) : null;
    }

    /**
     * Récupère tous les livres d'un membre (sa bibliothèque).
     * @param int $userId
     * @return array : un tableau d'objets Book.
     */
    public function getBooksByUserId(int $userId): array
    {
        $sql = self::SELECT_WITH_OWNER . " WHERE book.user_id = :userId ORDER BY book.created_at DESC";
        return $this->createBooks($this->db->query($sql, ['userId' => $userId]));
    }

    /**
     * Ajoute un livre dans la bibliothèque de son propriétaire.
     * @param Book $book
     * @return int : l'id du livre créé.
     */
    public function addBook(Book $book): int
    {
        $sql = "INSERT INTO book (user_id, title, author, image, description, is_available, created_at)
                VALUES (:userId, :title, :author, :image, :description, :isAvailable, NOW())";
        $this->db->query($sql, [
            'userId' => $book->getUserId(),
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'image' => $book->getImage(),
            'description' => $book->getDescription(),
            'isAvailable' => (int) $book->isAvailable(),
        ]);
        return (int) $this->db->getPDO()->lastInsertId();
    }

    /**
     * Modifie un livre. La date de mise à jour (updated_at) est gérée automatiquement par MySQL
     * grâce à "ON UPDATE CURRENT_TIMESTAMP" dans la définition de la table.
     * @param Book $book
     * @return void
     */
    public function updateBook(Book $book): void
    {
        $sql = "UPDATE book
                SET title = :title, author = :author, image = :image, description = :description, is_available = :isAvailable
                WHERE id = :id";
        $this->db->query($sql, [
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'image' => $book->getImage(),
            'description' => $book->getDescription(),
            'isAvailable' => (int) $book->isAvailable(),
            'id' => $book->getId(),
        ]);
    }

    /**
     * Supprime un livre.
     * @param int $id
     * @return void
     */
    public function deleteBook(int $id): void
    {
        $sql = "DELETE FROM book WHERE id = :id";
        $this->db->query($sql, ['id' => $id]);
    }

    /**
     * Transforme le résultat d'une requête en tableau d'objets Book.
     * @param PDOStatement $result
     * @return array
     */
    private function createBooks(PDOStatement $result): array
    {
        $books = [];
        while ($book = $result->fetch()) {
            $books[] = new Book($book);
        }
        return $books;
    }
}
