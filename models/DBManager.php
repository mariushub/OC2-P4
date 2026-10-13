<?php

/**
 * Classe qui permet de se connecter à la base de données.
 * Cette classe est un singleton : il n'existe qu'une seule instance (donc une seule connexion) pour toute la page.
 * Pour récupérer cette instance, il faut utiliser la méthode getInstance().
 */
class DBManager
{
    private static ?DBManager $instance = null;

    private PDO $db;

    /**
     * Constructeur privé : on ne peut pas faire "new DBManager()" depuis l'extérieur.
     * @see DBManager::getInstance()
     */
    private function __construct()
    {
        // utf8mb4 : le "vrai" UTF-8 de MySQL (accents, emojis, caractères spéciaux comme « » ou œ).
        $this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
        // En cas d'erreur SQL, PDO lance une exception (attrapée par le try/catch de index.php).
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Par défaut, chaque ligne est renvoyée sous forme de tableau associatif ['colonne' => valeur].
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    /**
     * Méthode qui permet de récupérer l'instance unique de la classe DBManager.
     * @return DBManager
     */
    public static function getInstance(): DBManager
    {
        if (self::$instance === null) {
            self::$instance = new DBManager();
        }
        return self::$instance;
    }

    /**
     * Méthode qui permet de récupérer l'objet PDO.
     * Utile par exemple pour lastInsertId().
     * @return PDO
     */
    public function getPDO(): PDO
    {
        return $this->db;
    }

    /**
     * Méthode qui permet d'exécuter une requête SQL.
     * Si des paramètres sont passés, on utilise une requête préparée (protection contre les injections SQL).
     * @param string $sql : la requête SQL à exécuter.
     * @param array|null $params : les paramètres de la requête SQL.
     * @return PDOStatement : le résultat de la requête SQL.
     */
    public function query(string $sql, ?array $params = null): PDOStatement
    {
        if ($params === null) {
            $query = $this->db->query($sql);
        } else {
            $query = $this->db->prepare($sql);
            $query->execute($params);
        }
        return $query;
    }
}
