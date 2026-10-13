<?php

/**
 * Entité Book : représente un livre de la bibliothèque d'un membre (une ligne de la table book).
 */
class Book extends AbstractEntity
{
    private int $userId = -1;
    private string $title = '';
    private string $author = '';
    private ?string $image = null;
    private string $description = '';
    private bool $isAvailable = true;
    private ?DateTime $createdAt = null;
    private ?DateTime $updatedAt = null;

    // Informations sur le propriétaire du livre.
    // Ce ne sont pas des colonnes de la table book : elles viennent d'une jointure avec la table user
    // (voir les alias "owner_pseudo" et "owner_avatar" dans BookManager).
    private string $ownerPseudo = '';
    private ?string $ownerAvatar = null;

    /**
     * Setter pour l'id du propriétaire.
     * @param int $userId
     * @return void
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Getter pour l'id du propriétaire.
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Setter pour le titre.
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Getter pour le titre.
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Setter pour l'auteur.
     * @param string $author
     * @return void
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    /**
     * Getter pour l'auteur.
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Setter pour l'image (nom du fichier). Peut être null si le livre n'a pas encore d'image.
     * @param string|null $image
     * @return void
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    /**
     * Getter pour l'image.
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Renvoie le chemin de l'image à afficher : celle du livre, ou l'image par défaut s'il n'en a pas.
     * @return string
     */
    public function getImagePath(): string
    {
        return $this->image ? BOOK_IMAGE_PATH . $this->image : DEFAULT_BOOK_IMAGE;
    }

    /**
     * Setter pour la description.
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Getter pour la description.
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Setter pour la disponibilité.
     * En base, c'est un TINYINT (0 ou 1) : PHP le convertit automatiquement en booléen grâce au typage.
     * @param bool $isAvailable
     * @return void
     */
    public function setIsAvailable(bool $isAvailable): void
    {
        $this->isAvailable = $isAvailable;
    }

    /**
     * Getter pour la disponibilité.
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    /**
     * Setter pour la date de création.
     * La base de données renvoie une chaîne ("2026-10-12 10:00:00") : on la convertit en objet DateTime.
     * @param string|DateTime $createdAt
     * @return void
     */
    public function setCreatedAt(string|DateTime $createdAt): void
    {
        if (is_string($createdAt)) {
            $createdAt = new DateTime($createdAt);
        }
        $this->createdAt = $createdAt;
    }

    /**
     * Getter pour la date de création.
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * Setter pour la date de mise à jour (null si le livre n'a jamais été modifié).
     * @param string|DateTime|null $updatedAt
     * @return void
     */
    public function setUpdatedAt(string|DateTime|null $updatedAt): void
    {
        if (is_string($updatedAt)) {
            $updatedAt = new DateTime($updatedAt);
        }
        $this->updatedAt = $updatedAt;
    }

    /**
     * Getter pour la date de mise à jour.
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Setter pour le pseudo du propriétaire.
     * @param string $ownerPseudo
     * @return void
     */
    public function setOwnerPseudo(string $ownerPseudo): void
    {
        $this->ownerPseudo = $ownerPseudo;
    }

    /**
     * Getter pour le pseudo du propriétaire.
     * @return string
     */
    public function getOwnerPseudo(): string
    {
        return $this->ownerPseudo;
    }

    /**
     * Setter pour l'avatar du propriétaire.
     * @param string|null $ownerAvatar
     * @return void
     */
    public function setOwnerAvatar(?string $ownerAvatar): void
    {
        $this->ownerAvatar = $ownerAvatar;
    }

    /**
     * Getter pour l'avatar du propriétaire.
     * @return string|null
     */
    public function getOwnerAvatar(): ?string
    {
        return $this->ownerAvatar;
    }

    /**
     * Renvoie le chemin de l'avatar du propriétaire, ou l'avatar par défaut.
     * @return string
     */
    public function getOwnerAvatarPath(): string
    {
        return $this->ownerAvatar ? AVATAR_PATH . $this->ownerAvatar : DEFAULT_AVATAR;
    }
}
