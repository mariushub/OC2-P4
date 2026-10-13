<?php

/**
 * Classe qui gère les membres : toutes les requêtes SQL sur la table user sont ici.
 */
class UserManager extends AbstractEntityManager
{
    /**
     * Récupère un membre par son id.
     * @param int $id
     * @return User|null : le membre, ou null s'il n'existe pas.
     */
    public function getUserById(int $id): ?User
    {
        $sql = "SELECT * FROM user WHERE id = :id";
        $user = $this->db->query($sql, ['id' => $id])->fetch();
        return $user ? new User($user) : null;
    }

    /**
     * Récupère un membre par son email (utilisé pour la connexion).
     * @param string $email
     * @return User|null : le membre, ou null s'il n'existe pas.
     */
    public function getUserByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM user WHERE email = :email";
        $user = $this->db->query($sql, ['email' => $email])->fetch();
        return $user ? new User($user) : null;
    }

    /**
     * Vérifie si un pseudo est déjà utilisé par un autre membre.
     * @param string $pseudo
     * @param int $excludedUserId : membre à ignorer (utile quand un membre modifie son propre profil).
     * @return bool
     */
    public function isPseudoTaken(string $pseudo, int $excludedUserId = -1): bool
    {
        $sql = "SELECT COUNT(*) FROM user WHERE pseudo = :pseudo AND id != :id";
        return $this->db->query($sql, ['pseudo' => $pseudo, 'id' => $excludedUserId])->fetchColumn() > 0;
    }

    /**
     * Vérifie si un email est déjà utilisé par un autre membre.
     * @param string $email
     * @param int $excludedUserId : membre à ignorer (utile quand un membre modifie son propre profil).
     * @return bool
     */
    public function isEmailTaken(string $email, int $excludedUserId = -1): bool
    {
        $sql = "SELECT COUNT(*) FROM user WHERE email = :email AND id != :id";
        return $this->db->query($sql, ['email' => $email, 'id' => $excludedUserId])->fetchColumn() > 0;
    }

    /**
     * Ajoute un nouveau membre en base de données.
     * @param User $user : le membre à ajouter (son mot de passe doit déjà être hashé).
     * @return int : l'id du membre créé.
     */
    public function createUser(User $user): int
    {
        $sql = "INSERT INTO user (pseudo, email, password, created_at) VALUES (:pseudo, :email, :password, NOW())";
        $this->db->query($sql, [
            'pseudo' => $user->getPseudo(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
        ]);
        return (int) $this->db->getPDO()->lastInsertId();
    }

    /**
     * Met à jour le pseudo, l'email et le mot de passe d'un membre.
     * @param User $user : le membre modifié (son mot de passe doit déjà être hashé).
     * @return void
     */
    public function updateUser(User $user): void
    {
        $sql = "UPDATE user SET pseudo = :pseudo, email = :email, password = :password WHERE id = :id";
        $this->db->query($sql, [
            'pseudo' => $user->getPseudo(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'id' => $user->getId(),
        ]);
    }

    /**
     * Change la photo de profil d'un membre.
     * @param int $userId
     * @param string $avatar : le nom du fichier image.
     * @return void
     */
    public function updateAvatar(int $userId, string $avatar): void
    {
        $sql = "UPDATE user SET avatar = :avatar WHERE id = :id";
        $this->db->query($sql, ['avatar' => $avatar, 'id' => $userId]);
    }
}
