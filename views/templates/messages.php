<?php
/**
 * Messagerie : liste des conversations à gauche, fil de discussion à droite.
 *
 * Variables envoyées par le contrôleur :
 *      $conversations Message[] : le dernier message de chaque conversation (avec les infos du contact).
 *      $contact User|null : le contact de la conversation ouverte (null si aucune).
 *      $messages Message[] : les messages de la conversation ouverte.
 *      $userId int : l'id du membre connecté.
 */
?>

<section class="messaging">
    <div class="container messaging-layout">
        <div class="conversations">
            <h1 class="conversations-title">Messagerie</h1>

            <?php if (empty($conversations)) { ?>
                <p class="conversations-empty">Aucune conversation pour le moment.</p>
            <?php } else { ?>
                <ul class="conversation-list">
                    <?php foreach ($conversations as $conversation) {
                        $isCurrent = $contact !== null && $conversation->getContactId() === $contact->getId();
                        // La conversation ouverte vient d'être lue : on ne la met pas en évidence.
                        $hasUnread = !$isCurrent && $conversation->getUnreadCount() > 0;
                        ?>
                        <li>
                            <a href="index.php?action=messages&to=<?= $conversation->getContactId() ?>"
                               class="conversation<?= $isCurrent ? ' is-current' : '' ?><?= $hasUnread ? ' is-unread' : '' ?>"<?= $isCurrent ? ' aria-current="page"' : '' ?>>
                                <img src="<?= htmlspecialchars($conversation->getContactAvatarPath()) ?>" alt="" width="48" height="48">
                                <span class="conversation-info">
                                    <span class="conversation-header">
                                        <span class="conversation-pseudo"><?= htmlspecialchars($conversation->getContactPseudo()) ?></span>
                                        <span class="conversation-date"><?= Utils::formatShortDate($conversation->getCreatedAt()) ?></span>
                                    </span>
                                    <span class="conversation-excerpt"><?= htmlspecialchars($conversation->getContent()) ?></span>
                                </span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>

        <div class="thread">
            <?php if ($contact === null) { ?>
                <p class="thread-empty">Pour démarrer une conversation, ouvrez la page d'un livre et cliquez sur « Envoyer un message ».</p>
            <?php } else { ?>
                <h2 class="thread-header">
                    <a href="index.php?action=profile&id=<?= $contact->getId() ?>">
                        <img src="<?= htmlspecialchars($contact->getAvatarPath()) ?>" alt="" width="48" height="48">
                        <?= htmlspecialchars($contact->getPseudo()) ?>
                    </a>
                </h2>

                <div class="thread-messages" id="thread-messages">
                    <?php if (empty($messages)) { ?>
                        <p class="thread-start">Écrivez votre premier message à <?= htmlspecialchars($contact->getPseudo()) ?> !</p>
                    <?php } ?>

                    <?php foreach ($messages as $message) {
                        $isMine = $message->getSenderId() === $userId;
                        ?>
                        <div class="message <?= $isMine ? 'message-mine' : 'message-theirs' ?>">
                            <p class="message-meta">
                                <?php if (!$isMine) { ?>
                                    <img src="<?= htmlspecialchars($contact->getAvatarPath()) ?>" alt="" width="24" height="24">
                                <?php } ?>
                                <time datetime="<?= $message->getCreatedAt()->format('Y-m-d\TH:i') ?>"><?= $message->getCreatedAt()->format('d.m H:i') ?></time>
                            </p>
                            <div class="message-content"><?= Utils::format($message->getContent()) ?></div>
                        </div>
                    <?php } ?>
                </div>

                <form action="index.php?action=sendMessage" method="post" class="thread-form">
                    <input type="hidden" name="csrfToken" value="<?= Utils::getCsrfToken() ?>">
                    <input type="hidden" name="receiverId" value="<?= $contact->getId() ?>">
                    <label for="new-message" class="visually-hidden">Votre message</label>
                    <textarea id="new-message" name="content" rows="1" maxlength="2000" placeholder="Tapez votre message ici" required></textarea>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </form>
            <?php } ?>
        </div>
    </div>
</section>
