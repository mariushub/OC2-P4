<?php

/**
 * Classe mère de tous les managers (BookManager, UserManager...).
 * Un manager contient les requêtes SQL d'une table. Il récupère automatiquement la connexion à la base de données.
 */
abstract class AbstractEntityManager
{
    protected DBManager $db;

    /**
     * Constructeur de la classe.
     * Il récupère automatiquement l'instance de DBManager.
     */
    public function __construct()
    {
        $this->db = DBManager::getInstance();
    }
}
