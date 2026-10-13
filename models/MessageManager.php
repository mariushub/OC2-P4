<?php

/**
 * Classe qui gère la messagerie : toutes les requêtes SQL sur la table message sont ici.
 */
class MessageManager extends AbstractEntityManager
{
    /**
     * Récupère la liste des conversations d'un membre : pour chaque contact, le dernier message échangé,
     * les informations du contact et le nombre de messages non lus.
     * @param int $userId : le membre connecté.
     * @return array : un tableau d'objets Message (un par conversation), du plus récent au plus ancien.
     */
    public function getConversations(int $userId): array
    {
        // Comment ça marche :
        // 1. La sous-requête du WHERE regroupe les messages du membre par contact ("l'autre personne" :
        //    le destinataire si c'est lui qui a écrit, sinon l'expéditeur) et garde le plus grand id,
        //    c'est-à-dire le dernier message de chaque conversation.
        // 2. On joint la table user pour récupérer le pseudo et l'avatar du contact.
        // 3. Une autre sous-requête compte les messages du contact pas encore lus par le membre.
        $sql = "SELECT message.*,
                       contact.id AS contact_id, contact.pseudo AS contact_pseudo, contact.avatar AS contact_avatar,
                       (SELECT COUNT(*) FROM message AS unread
                        WHERE unread.sender_id = contact.id AND unread.receiver_id = :userId1 AND unread.is_read = 0) AS unread_count
                FROM message
                INNER JOIN user AS contact
                        ON contact.id = IF(message.sender_id = :userId2, message.receiver_id, message.sender_id)
                WHERE message.id IN (
                    SELECT MAX(id) FROM message
                    WHERE sender_id = :userId3 OR receiver_id = :userId4
                    GROUP BY IF(sender_id = :userId5, receiver_id, sender_id)
                )
                ORDER BY message.created_at DESC";

        // Chaque marqueur doit avoir un nom différent : on donne la même valeur aux 5.
        $params = array_fill_keys(['userId1', 'userId2', 'userId3', 'userId4', 'userId5'], $userId);
        $result = $this->db->query($sql, $params);

        $conversations = [];
        while ($message = $result->fetch()) {
            $conversations[] = new Message($message);
        }
        return $conversations;
    }

    /**
     * Récupère tous les messages échangés entre deux membres (le fil de discussion), du plus ancien au plus récent.
     * @param int $userId
     * @param int $contactId
     * @return array : un tableau d'objets Message.
     */
    public function getMessagesBetween(int $userId, int $contactId): array
    {
        $sql = "SELECT * FROM message
                WHERE (sender_id = :userId1 AND receiver_id = :contactId1)
                   OR (sender_id = :contactId2 AND receiver_id = :userId2)
                ORDER BY created_at ASC, id ASC";
        $result = $this->db->query($sql, [
            'userId1' => $userId,
            'contactId1' => $contactId,
            'contactId2' => $contactId,
            'userId2' => $userId,
        ]);

        $messages = [];
        while ($message = $result->fetch()) {
            $messages[] = new Message($message);
        }
        return $messages;
    }

    /**
     * Marque comme lus tous les messages qu'un contact a envoyés au membre.
     * @param int $userId : le membre qui lit la conversation.
     * @param int $contactId : le contact.
     * @return void
     */
    public function markAsRead(int $userId, int $contactId): void
    {
        $sql = "UPDATE message SET is_read = 1 WHERE sender_id = :contactId AND receiver_id = :userId AND is_read = 0";
        $this->db->query($sql, ['contactId' => $contactId, 'userId' => $userId]);
    }

    /**
     * Compte les messages non lus d'un membre (pour le compteur du menu).
     * @param int $userId
     * @return int
     */
    public function countUnread(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM message WHERE receiver_id = :userId AND is_read = 0";
        return (int) $this->db->query($sql, ['userId' => $userId])->fetchColumn();
    }

    /**
     * Enregistre un nouveau message.
     * @param Message $message
     * @return void
     */
    public function addMessage(Message $message): void
    {
        $sql = "INSERT INTO message (sender_id, receiver_id, content, is_read, created_at)
                VALUES (:senderId, :receiverId, :content, 0, NOW())";
        $this->db->query($sql, [
            'senderId' => $message->getSenderId(),
            'receiverId' => $message->getReceiverId(),
            'content' => $message->getContent(),
        ]);
    }
}
