<?php

/**
 * Classe utilitaire : elle ne contient que des méthodes statiques, appelables sans créer d'objet.
 * Exemple : Utils::redirect('home');
 */
class Utils
{
    /**
     * Redirige vers une page du site.
     * @param string $action : l'action voulue (correspond aux "case" du routeur dans index.php).
     * @param array $params : facultatif, les paramètres de l'URL sous la forme ['id' => 3].
     * @return void
     */
    public static function redirect(string $action, array $params = []): void
    {
        $url = 'index.php?' . http_build_query(['action' => $action] + $params);
        header("Location: $url");
        exit();
    }

    /**
     * Récupère une variable envoyée par l'utilisateur (dans l'URL avec GET ou par un formulaire avec POST).
     * Si cette variable n'existe pas, on retourne la valeur par défaut.
     * @param string $variableName : le nom de la variable à récupérer.
     * @param mixed $defaultValue : la valeur par défaut si la variable n'est pas définie.
     * @return mixed : la valeur de la variable ou la valeur par défaut.
     */
    public static function request(string $variableName, mixed $defaultValue = null): mixed
    {
        $value = $_REQUEST[$variableName] ?? $defaultValue;

        // Le site n'attend que des valeurs simples. Une URL trafiquée comme "?search[]=x" enverrait un tableau,
        // qui ferait planter les fonctions de texte (trim...) : dans ce cas, on utilise la valeur par défaut.
        return is_array($value) ? $defaultValue : $value;
    }

    /**
     * Protège un texte contre les failles XSS et transforme ses retours à la ligne en paragraphes <p>.
     * Utile pour les descriptions de livres et les messages, qui peuvent faire plusieurs paragraphes.
     * @param string $text
     * @return string : le HTML prêt à être affiché.
     */
    public static function format(string $text): string
    {
        $html = '';
        foreach (explode("\n", htmlspecialchars($text, ENT_QUOTES)) as $line) {
            if (trim($line) !== '') {
                $html .= '<p>' . $line . '</p>';
            }
        }
        return $html;
    }

    /**
     * Raccourcit un texte trop long et ajoute "..." à la fin.
     * mb_ (multibyte) : compte correctement les caractères accentués, qui prennent plusieurs octets en UTF-8.
     * @param string $text
     * @param int $length : le nombre maximum de caractères.
     * @return string
     */
    public static function truncate(string $text, int $length): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return rtrim(mb_substr($text, 0, $length)) . '...';
    }

    /**
     * Renvoie le temps écoulé depuis une date, en français (ex : "1 an", "3 mois", "5 jours").
     * Utilisé pour "Membre depuis ...".
     * @param DateTime $date
     * @return string
     */
    public static function timeSince(DateTime $date): string
    {
        $interval = $date->diff(new DateTime());

        if ($interval->y > 0) {
            return $interval->y . ($interval->y > 1 ? ' ans' : ' an');
        }
        if ($interval->m > 0) {
            return $interval->m . ' mois';
        }
        if ($interval->d > 0) {
            return $interval->d . ($interval->d > 1 ? ' jours' : ' jour');
        }
        return "aujourd'hui";
    }

    /**
     * Date courte de la liste des conversations : l'heure si c'est aujourd'hui ("15:43"), sinon le jour ("20.08").
     * @param DateTime $date
     * @return string
     */
    public static function formatShortDate(DateTime $date): string
    {
        $isToday = $date->format('Y-m-d') === date('Y-m-d');
        return $date->format($isToday ? 'H:i' : 'd.m');
    }

    /**
     * Indique si un membre est connecté.
     * @return bool
     */
    public static function isConnected(): bool
    {
        return isset($_SESSION['userId']);
    }

    /**
     * Renvoie l'id du membre connecté, ou null si personne n'est connecté.
     * @return int|null
     */
    public static function getConnectedUserId(): ?int
    {
        return $_SESSION['userId'] ?? null;
    }

    /**
     * À appeler au début de chaque page réservée aux membres.
     * Si personne n'est connecté, on redirige vers la page de connexion.
     * @return int : l'id du membre connecté.
     */
    public static function checkIfUserIsConnected(): int
    {
        if (!self::isConnected()) {
            self::redirect('loginForm');
        }
        return self::getConnectedUserId();
    }

    /**
     * Renvoie le jeton anti-CSRF de la session (il est créé la première fois).
     * Il est ajouté dans un champ caché de chaque formulaire POST du site.
     * @return string
     */
    public static function getCsrfToken(): string
    {
        if (!isset($_SESSION['csrfToken'])) {
            // random_bytes() génère des octets aléatoires impossibles à deviner.
            $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrfToken'];
    }

    /**
     * Vérifie que le formulaire envoyé contient bien le jeton anti-CSRF de la session.
     * Faille CSRF : un site malveillant pourrait faire envoyer un formulaire à notre site par un membre connecté,
     * à son insu (ex : supprimer un de ses livres). Ce site ne connaît pas le jeton : sa requête est refusée.
     * On ne lit que $_POST : une simple visite de lien (GET) ne peut donc jamais modifier des données.
     * @throws Exception : si le jeton est absent ou incorrect.
     * @return void
     */
    public static function checkCsrfToken(): void
    {
        $token = $_POST['csrfToken'] ?? '';
        // hash_equals() compare deux chaînes en un temps constant (plus sûr que === pour un secret).
        if (!is_string($token) || !isset($_SESSION['csrfToken']) || !hash_equals($_SESSION['csrfToken'], $token)) {
            throw new Exception('Le formulaire a expiré, veuillez réessayer.', 403);
        }
    }

    /**
     * Enregistre un message à afficher sur la prochaine page (ex : "Votre livre a été ajouté.").
     * Il est stocké en session car on fait une redirection juste après.
     * @param string $message
     * @param string $type : "success" ou "error".
     * @return void
     */
    public static function addFlash(string $message, string $type = 'success'): void
    {
        $_SESSION['flashes'][] = ['message' => $message, 'type' => $type];
    }

    /**
     * Récupère les messages à afficher, puis les supprime de la session (ils ne s'affichent qu'une fois).
     * @return array
     */
    public static function getFlashes(): array
    {
        $flashes = $_SESSION['flashes'] ?? [];
        unset($_SESSION['flashes']);
        return $flashes;
    }
}
