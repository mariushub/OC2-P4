-- =====================================================================
-- Base de données du site TomTroc
-- Ce fichier crée la base, les 3 tables, puis insère des données de test (fixtures).
-- Il peut être réimporté autant de fois que nécessaire : les tables sont supprimées puis recréées.
--
-- Comptes de test : tous les utilisateurs ont le mot de passe "password123".
-- Compte principal pour tester : nathalie@mail.com / password123
-- =====================================================================

CREATE DATABASE IF NOT EXISTS tomtroc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tomtroc;

-- On supprime dans l'ordre inverse des dépendances (message et book dépendent de user).
DROP TABLE IF EXISTS `message`;
DROP TABLE IF EXISTS `book`;
DROP TABLE IF EXISTS `user`;

-- ---------------------------------------------------------------------
-- Table user : les membres du site.
-- ---------------------------------------------------------------------
CREATE TABLE `user` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `pseudo` VARCHAR(50) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,           -- hash généré par password_hash(), jamais le mot de passe en clair
    `avatar` VARCHAR(255) NULL DEFAULT NULL,    -- nom du fichier image, NULL = avatar par défaut
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- sert pour "Membre depuis 1 an"
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_user_pseudo` (`pseudo`),
    UNIQUE KEY `uniq_user_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table book : les livres de la bibliothèque de chaque membre.
-- Relation : un user possède 0..N livres, un livre appartient à 1 seul user.
-- ---------------------------------------------------------------------
CREATE TABLE `book` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,            -- le propriétaire du livre
    `title` VARCHAR(255) NOT NULL,
    `author` VARCHAR(255) NOT NULL,
    `image` VARCHAR(255) NULL DEFAULT NULL,     -- peut rester vide à la création
    `description` TEXT NOT NULL,                -- TEXT : peut être un très long texte
    `is_available` TINYINT(1) NOT NULL DEFAULT 1,  -- 1 = disponible, 0 = non dispo.
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_book_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table message : la messagerie.
-- Relations : un message a 1 expéditeur (sender) et 1 destinataire (receiver), tous deux des users.
-- Une "conversation" = tous les messages échangés entre deux mêmes utilisateurs.
-- ---------------------------------------------------------------------
CREATE TABLE `message` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `sender_id` INT UNSIGNED NOT NULL,
    `receiver_id` INT UNSIGNED NOT NULL,
    `content` TEXT NOT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,    -- sert pour le compteur de la messagerie dans le menu
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_message_sender` FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_message_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================================
-- FIXTURES (données de test reprises de la maquette)
-- =====================================================================

-- Mot de passe de tous les comptes : password123
INSERT INTO `user` (`id`, `pseudo`, `email`, `password`, `avatar`, `created_at`) VALUES
(1,  'nathalire',       'nathalie@mail.com',   '$2y$10$.40TfdpfUbSLcV7iqtnXYeT5EuowWlK3WOcaygb7DEKHaatPY1gBK', 'nathalire.jpg', '2025-09-10 14:00:00'),
(2,  'Alexlecture',     'alex@mail.com',       '$2y$10$.40TfdpfUbSLcV7iqtnXYeT5EuowWlK3WOcaygb7DEKHaatPY1gBK', 'alexlecture.jpg', '2025-08-02 09:30:00'),
(3,  'CamilleClubLit',  'camille@mail.com',    '$2y$10$.40TfdpfUbSLcV7iqtnXYeT5EuowWlK3WOcaygb7DEKHaatPY1gBK', NULL, '2025-11-15 18:20:00'),
(4,  'Hugo1990_12',     'hugo@mail.com',       '$2y$10$.40TfdpfUbSLcV7iqtnXYeT5EuowWlK3WOcaygb7DEKHaatPY1gBK', NULL, '2026-01-20 11:00:00'),
(5,  'Juju1432',        'juju@mail.com',       '$2y$10$.40TfdpfUbSLcV7iqtnXYeT5EuowWlK3WOcaygb7DEKHaatPY1gBK', NULL, '2026-02-03 16:45:00'),
(6,  'Christiane75014', 'christiane@mail.com', '$2y$10$.40TfdpfUbSLcV7iqtnXYeT5EuowWlK3WOcaygb7DEKHaatPY1gBK', NULL, '2025-06-12 10:10:00'),
(7,  'Hamzalecture',    'hamza@mail.com',      '$2y$10$.40TfdpfUbSLcV7iqtnXYeT5EuowWlK3WOcaygb7DEKHaatPY1gBK', NULL, '2026-03-08 20:00:00'),
(8,  'Lou&Ben50',       'louben@mail.com',     '$2y$10$.40TfdpfUbSLcV7iqtnXYeT5EuowWlK3WOcaygb7DEKHaatPY1gBK', NULL, '2026-04-21 08:15:00'),
(9,  'Lolobzh',         'lolo@mail.com',       '$2y$10$.40TfdpfUbSLcV7iqtnXYeT5EuowWlK3WOcaygb7DEKHaatPY1gBK', NULL, '2025-12-01 13:40:00'),
(10, 'Sas634',          'sas@mail.com',        '$2y$10$.40TfdpfUbSLcV7iqtnXYeT5EuowWlK3WOcaygb7DEKHaatPY1gBK', 'sas634.jpg', '2026-05-14 17:25:00'),
(11, 'ML95',            'ml95@mail.com',       '$2y$10$.40TfdpfUbSLcV7iqtnXYeT5EuowWlK3WOcaygb7DEKHaatPY1gBK', NULL, '2026-02-27 12:00:00'),
(12, 'Verogo33',        'vero@mail.com',       '$2y$10$.40TfdpfUbSLcV7iqtnXYeT5EuowWlK3WOcaygb7DEKHaatPY1gBK', NULL, '2025-10-05 19:05:00'),
(13, 'AnnikaBrahms',    'annika@mail.com',     '$2y$10$.40TfdpfUbSLcV7iqtnXYeT5EuowWlK3WOcaygb7DEKHaatPY1gBK', NULL, '2026-06-30 09:50:00'),
(14, 'Victoirefabr912', 'victoire@mail.com',   '$2y$10$.40TfdpfUbSLcV7iqtnXYeT5EuowWlK3WOcaygb7DEKHaatPY1gBK', NULL, '2026-07-18 15:35:00'),
(15, 'Lotrfanclub67',   'lotr@mail.com',       '$2y$10$.40TfdpfUbSLcV7iqtnXYeT5EuowWlK3WOcaygb7DEKHaatPY1gBK', NULL, '2026-08-09 21:10:00');

-- Les images sont dans uploads/books/ et les avatars dans uploads/avatars/.
-- Les 16 livres de la page "Nos livres à l'échange", + 3 livres pour nathalire (page "Mon compte").
-- Les dates de création sont décroissantes pour que l'accueil affiche les 4 mêmes livres que la maquette.
INSERT INTO `book` (`user_id`, `title`, `author`, `image`, `description`, `is_available`, `created_at`) VALUES
(3,  'Esther', 'Alabaster', 'esther.jpg', 'Une très belle édition du livre d''Esther, illustrée de photographies magnifiques. Un livre qui se lit lentement et se contemple autant qu''il se lit.', 1, '2026-10-12 10:00:00'),
(2,  'The Kinfolk Table', 'Nathan Williams', 'the-kinfolk-table.jpg', 'J''ai récemment plongé dans les pages de ''The Kinfolk Table'' et j''ai été enchanté par cette œuvre captivante. Ce livre va bien au-delà d''une simple collection de recettes ; il célèbre l''art de partager des moments authentiques autour de la table.

Les photographies magnifiques et le ton chaleureux captivent dès le départ, transportant le lecteur dans un voyage à travers des recettes et des histoires qui mettent en avant la beauté de la simplicité et de la convivialité.

Chaque page est une invitation à ralentir, à savourer et à créer des souvenirs durables avec les êtres chers.

''The Kinfolk Table'' incarne parfaitement l''esprit de la cuisine et de la camaraderie, et il est certain que ce livre trouvera une place spéciale dans le cœur de tout amoureux de la cuisine et des rencontres inspirantes.', 1, '2026-10-11 10:00:00'),
(2,  'Wabi Sabi', 'Beth Kempton', 'wabi-sabi.jpg', 'Un livre apaisant sur la philosophie japonaise de l''imperfection. Parfait pour ralentir et apprécier les petites choses du quotidien.', 1, '2026-10-10 10:00:00'),
(4,  'Milk & honey', 'Rupi Kaur', 'milk-and-honey.jpg', 'Un recueil de poèmes bouleversant sur l''amour, la perte et la guérison. Je l''ai lu deux fois, il mérite maintenant un nouveau lecteur.', 1, '2026-10-09 10:00:00'),
(5,  'Delight!', 'Justin Rossow', 'delight.jpg', 'Un livre plein d''optimisme, qui donne envie de retrouver la joie dans les petites choses. Je le prête en ce moment à une amie.', 0, '2026-10-08 10:00:00'),
(6,  'Milwaukee Mission', 'Elder Cooper Low', 'milwaukee-mission.jpg', 'Un récit de voyage touchant, raconté avec beaucoup de sincérité. Couverture rigide en très bon état.', 1, '2026-10-07 10:00:00'),
(7,  'Minimalist Graphics', 'Julia Schonlau', 'minimalist-graphics.jpg', 'Une mine d''inspiration pour tous ceux qui aiment le design graphique épuré. Beaucoup d''exemples très bien présentés.', 1, '2026-10-06 10:00:00'),
(4,  'Hygge', 'Meik Wiking', 'hygge.jpg', 'Tout savoir sur l''art de vivre danois : le confort, les bougies, les moments partagés. Idéal pour l''hiver !', 1, '2026-10-05 10:00:00'),
(8,  'Innovation', 'Matt Ridley', 'innovation.jpg', 'Un essai passionnant sur la façon dont naissent les innovations, avec beaucoup d''exemples historiques.', 1, '2026-10-04 10:00:00'),
(9,  'Psalms', 'Alabaster', 'psalms.jpg', 'Le livre des Psaumes dans une magnifique édition illustrée. Comme neuf.', 1, '2026-10-03 10:00:00'),
(10, 'Thinking, Fast & Slow', 'Daniel Kahneman', 'thinking-fast-and-slow.jpg', 'Un classique sur la façon dont notre cerveau prend des décisions. Dense mais passionnant.', 0, '2026-10-02 10:00:00'),
(11, 'A Book Full Of Hope', 'Rupi Kaur', 'a-book-full-of-hope.jpg', 'Un recueil lumineux, à lire quand on a besoin d''un peu de douceur.', 1, '2026-10-01 10:00:00'),
(12, 'The Subtle Art Of Not Giving A F*ck', 'Mark Manson', 'the-subtle-art.jpg', 'Un livre de développement personnel qui sort de l''ordinaire, drôle et direct.', 1, '2026-09-30 10:00:00'),
(13, 'Narnia', 'C.S Lewis', 'narnia.jpg', 'L''intégrale des Chroniques de Narnia, un incontournable de mon enfance.', 0, '2026-09-29 10:00:00'),
(14, 'Company Of One', 'Paul Jarvis', 'company-of-one.jpg', 'Une réflexion intéressante sur le fait de rester petit plutôt que de vouloir grandir à tout prix.', 1, '2026-09-28 10:00:00'),
(15, 'The Two Towers', 'J.R.R Tolkien', 'the-two-towers.jpg', 'Le deuxième tome du Seigneur des Anneaux, en version originale anglaise.', 1, '2026-09-27 10:00:00'),
(1,  'Le Petit Prince', 'Antoine de Saint-Exupéry', NULL, 'Un conte poétique que l''on redécouvre à chaque âge. Petite édition de poche.', 1, '2026-05-02 10:00:00'),
(1,  'L''Étranger', 'Albert Camus', NULL, 'Le grand classique de Camus. Quelques annotations au crayon dans les marges.', 0, '2026-04-11 10:00:00'),
(1,  'Sapiens', 'Yuval Noah Harari', NULL, 'Une brève histoire de l''humanité, passionnante du début à la fin.', 1, '2026-03-15 10:00:00');

-- Quelques conversations pour tester la messagerie.
-- Le dernier message d'Alexlecture à nathalire n'est pas lu : le menu doit afficher "Messagerie 1".
-- La conversation Hugo1990_12 / Juju1432 ne doit PAS être visible quand on est connecté avec nathalire.
INSERT INTO `message` (`sender_id`, `receiver_id`, `content`, `is_read`, `created_at`) VALUES
(1,  2,  'Bonjour ! Votre exemplaire de « The Kinfolk Table » est-il toujours disponible ?', 1, '2026-10-13 15:35:00'),
(2,  1,  'Bonjour ! Oui, il est toujours disponible.', 1, '2026-10-13 15:41:00'),
(2,  1,  'Qu''aimeriez-vous me proposer en échange ? J''ai vu « Sapiens » dans votre bibliothèque…', 0, '2026-10-13 15:43:00'),
(1,  3,  'Bonjour Camille, votre exemplaire d''Esther m''intéresse beaucoup !', 1, '2026-10-12 18:05:00'),
(10, 1,  'Bonjour, j''ai vu que vous aviez « L''Étranger », c''est un de mes livres préférés !', 1, '2026-09-15 09:12:00'),
(1,  10, 'Bonjour ! Il n''est plus disponible pour le moment, je vous préviens dès qu''il l''est.', 1, '2026-09-15 10:30:00'),
(4,  5,  'Salut ! Delight! m''intéresse, il sera bientôt disponible ?', 0, '2026-10-09 11:00:00');
