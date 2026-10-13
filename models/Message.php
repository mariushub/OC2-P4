<?php

/**
 * Entité Message : représente un message envoyé d'un membre à un autre (une ligne de la table message).
 */
class Message extends AbstractEntity
{
    private int $senderId = -1;
    private int $receiverId = -1;
    private string $content = '';
    private bool $isRead = false;
    private ?DateTime $createdAt = null;

    // Informations sur "l'autre personne" de la conversation (le contact), utilisées dans la liste
    // des conversations. Elles viennent d'une jointure (alias contact_...), pas de la table message.
    private int $contactId = -1;
    private string $contactPseudo = '';
    private ?string $contactAvatar = null;
    private int $unreadCount = 0;

    /**
     * Setter pour l'id de l'expéditeur.
     * @param int $senderId
     * @return void
     */
    public function setSenderId(int $senderId): void
    {
        $this->senderId = $senderId;
    }

    /**
     * Getter pour l'id de l'expéditeur.
     * @return int
     */
    public function getSenderId(): int
    {
        return $this->senderId;
    }

    /**
     * Setter pour l'id du destinataire.
     * @param int $receiverId
     * @return void
     */
    public function setReceiverId(int $receiverId): void
    {
        $this->receiverId = $receiverId;
    }

    /**
     * Getter pour l'id du destinataire.
     * @return int
     */
    public function getReceiverId(): int
    {
        return $this->receiverId;
    }

    /**
     * Setter pour le contenu du message.
     * @param string $content
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * Getter pour le contenu du message.
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Setter pour l'état "lu".
     * @param bool $isRead
     * @return void
     */
    public function setIsRead(bool $isRead): void
    {
        $this->isRead = $isRead;
    }

    /**
     * Indique si le message a été lu par son destinataire.
     * @return bool
     */
    public function isRead(): bool
    {
        return $this->isRead;
    }

    /**
     * Setter pour la date d'envoi.
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
     * Getter pour la date d'envoi.
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * Setter pour l'id du contact.
     * @param int $contactId
     * @return void
     */
    public function setContactId(int $contactId): void
    {
        $this->contactId = $contactId;
    }

    /**
     * Getter pour l'id du contact.
     * @return int
     */
    public function getContactId(): int
    {
        return $this->contactId;
    }

    /**
     * Setter pour le pseudo du contact.
     * @param string $contactPseudo
     * @return void
     */
    public function setContactPseudo(string $contactPseudo): void
    {
        $this->contactPseudo = $contactPseudo;
    }

    /**
     * Getter pour le pseudo du contact.
     * @return string
     */
    public function getContactPseudo(): string
    {
        return $this->contactPseudo;
    }

    /**
     * Setter pour l'avatar du contact.
     * @param string|null $contactAvatar
     * @return void
     */
    public function setContactAvatar(?string $contactAvatar): void
    {
        $this->contactAvatar = $contactAvatar;
    }

    /**
     * Renvoie le chemin de l'avatar du contact, ou l'avatar par défaut.
     * @return string
     */
    public function getContactAvatarPath(): string
    {
        return $this->contactAvatar ? AVATAR_PATH . $this->contactAvatar : DEFAULT_AVATAR;
    }

    /**
     * Setter pour le nombre de messages non lus de la conversation.
     * @param int $unreadCount
     * @return void
     */
    public function setUnreadCount(int $unreadCount): void
    {
        $this->unreadCount = $unreadCount;
    }

    /**
     * Getter pour le nombre de messages non lus de la conversation.
     * @return int
     */
    public function getUnreadCount(): int
    {
        return $this->unreadCount;
    }
}
