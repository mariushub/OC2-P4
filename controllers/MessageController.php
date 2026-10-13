<?php

/**
 * Contrôleur de la messagerie : liste des conversations, fil de discussion et envoi de messages.
 */
class MessageController
{
    /**
     * Affiche la messagerie.
     * Paramètre facultatif "to" : l'id du contact dont on veut voir la conversation
     * (c'est aussi ce qu'utilisent les boutons "Envoyer un message" des pages livre et profil).
     * Sans "to", on ouvre la conversation la plus récente.
     * @return void
     */
    public function showMessages(): void
    {
        $userId = Utils::checkIfUserIsConnected();

        $messageManager = new MessageManager();
        $conversations = $messageManager->getConversations($userId);

        $contactId = (int) Utils::request('to', 0);
        if ($contactId === 0 && !empty($conversations)) {
            $contactId = $conversations[0]->getContactId();
        }

        // On ne peut pas s'écrire à soi-même.
        if ($contactId === $userId) {
            Utils::redirect('messages');
        }

        $contact = null;
        $messages = [];
        if ($contactId !== 0) {
            $userManager = new UserManager();
            $contact = $userManager->getUserById($contactId);
            if ($contact === null) {
                throw new Exception("Ce membre n'existe pas.", 404);
            }

            // Ouvrir la conversation = lire les messages du contact : on les marque comme lus.
            $messageManager->markAsRead($userId, $contactId);
            $messages = $messageManager->getMessagesBetween($userId, $contactId);
        }

        $view = new View('Messagerie');
        $view->render('messages', [
            'conversations' => $conversations,
            'contact' => $contact,
            'messages' => $messages,
            'userId' => $userId,
        ]);
    }

    /**
     * Envoie un message au contact choisi.
     * @return void
     */
    public function sendMessage(): void
    {
        $userId = Utils::checkIfUserIsConnected();
        Utils::checkCsrfToken();

        $receiverId = (int) Utils::request('receiverId', 0);
        $content = trim(Utils::request('content', ''));

        $userManager = new UserManager();
        if ($receiverId === $userId || $userManager->getUserById($receiverId) === null) {
            throw new Exception("Ce destinataire n'existe pas.", 404);
        }

        if ($content === '') {
            Utils::addFlash('Votre message est vide.', 'error');
        } elseif (mb_strlen($content) > 2000) {
            Utils::addFlash('Votre message ne doit pas dépasser 2000 caractères.', 'error');
        } else {
            $message = new Message([
                'sender_id' => $userId,
                'receiver_id' => $receiverId,
                'content' => $content,
            ]);
            $messageManager = new MessageManager();
            $messageManager->addMessage($message);
        }

        Utils::redirect('messages', ['to' => $receiverId]);
    }
}
