<?php

/**
 * Classe mère de toutes les entités (Book, User, Message...).
 * Une entité est un objet PHP qui représente une ligne d'une table de la base de données.
 */
abstract class AbstractEntity
{
    // Par défaut l'id vaut -1, ce qui permet de savoir facilement si l'entité est nouvelle (pas encore en base) ou pas.
    protected int $id = -1;

    /**
     * Constructeur de la classe.
     * Si un tableau associatif est passé en paramètre, on hydrate l'entité.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    /**
     * Système d'hydratation de l'entité : remplit l'objet à partir d'un tableau associatif.
     * Pour chaque clé du tableau, on appelle le setter correspondant s'il existe.
     * Les underscores sont transformés en camelCase (ex : created_at appelle setCreatedAt).
     * @param array $data
     * @return void
     */
    protected function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . str_replace('_', '', ucwords($key, '_'));
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * Setter pour l'id.
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Getter pour l'id.
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
