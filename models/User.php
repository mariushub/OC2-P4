<?php

/**
 * Entité User : représente un membre du site (une ligne de la table user).
 */
class User extends AbstractEntity
{
    private string $pseudo = '';
    private string $email = '';
    private string $password = '';
    private ?string $avatar = null;
    private ?DateTime $createdAt = null;

    /**
     * Setter pour le pseudo.
     * @param string $pseudo
     * @return void
     */
    public function setPseudo(string $pseudo): void
    {
        $this->pseudo = $pseudo;
    }

    /**
     * Getter pour le pseudo.
     * @return string
     */
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    /**
     * Setter pour l'email.
     * @param string $email
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Getter pour l'email.
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Setter pour le mot de passe.
     * Attention : on y stocke toujours le hash (password_hash), jamais le mot de passe en clair.
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * Getter pour le mot de passe (hashé).
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Setter pour l'avatar (nom du fichier). Null si le membre n'a pas de photo.
     * @param string|null $avatar
     * @return void
     */
    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * Getter pour l'avatar.
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * Renvoie le chemin de l'avatar à afficher, ou l'avatar par défaut.
     * @return string
     */
    public function getAvatarPath(): string
    {
        return $this->avatar ? AVATAR_PATH . $this->avatar : DEFAULT_AVATAR;
    }

    /**
     * Setter pour la date d'inscription.
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
     * Getter pour la date d'inscription.
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }
}
