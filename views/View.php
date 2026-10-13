<?php

/**
 * Cette classe génère les vues en fonction de ce que chaque contrôleur lui passe en paramètre.
 */
class View
{
    /**
     * Le titre de la page (affiché dans l'onglet du navigateur).
     */
    private string $title;

    /**
     * Constructeur.
     * @param string $title : le titre de la page.
     */
    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * Affiche une page complète : le template de la page demandée, inséré dans le template principal (main.php).
     * @param string $viewName : le nom de la vue demandée par le contrôleur (ex : "home" pour home.php).
     * @param array $params : les données que le contrôleur envoie à la vue.
     * @return void
     */
    public function render(string $viewName, array $params = []): void
    {
        $viewPath = $this->buildViewPath($viewName);

        // Les variables ci-dessous sont utilisées dans main.php, le template principal.
        $content = $this->renderViewFromTemplate($viewPath, $params);
        $title = $this->title;
        $flashes = Utils::getFlashes();
        $unreadCount = $this->getUnreadCount();
        ob_start();
        require MAIN_VIEW_PATH;
        echo ob_get_clean();
    }

    /**
     * Génère le HTML de la vue demandée, sans l'afficher.
     * @param string $viewPath : le chemin de la vue demandée par le contrôleur.
     * @param array $params : les données envoyées par le contrôleur.
     * @throws Exception : si la vue n'existe pas.
     * @return string : le HTML de la vue.
     */
    private function renderViewFromTemplate(string $viewPath, array $params = []): string
    {
        if (!file_exists($viewPath)) {
            throw new Exception("La vue '$viewPath' est introuvable.");
        }

        // extract() transforme ['books' => $books] en une variable $books utilisable dans le template.
        extract($params);
        // ob_start() "capture" tout ce qui est affiché au lieu de l'envoyer au navigateur...
        ob_start();
        require $viewPath;
        // ... et ob_get_clean() le récupère sous forme de chaîne.
        return ob_get_clean();
    }

    /**
     * Nombre de messages non lus, affiché dans le menu de toutes les pages.
     * C'est une donnée du template principal (main.php) : plutôt que de la demander à chaque contrôleur,
     * on la récupère une seule fois ici.
     * @return int
     */
    private function getUnreadCount(): int
    {
        if (!Utils::isConnected()) {
            return 0;
        }
        try {
            $messageManager = new MessageManager();
            return $messageManager->countUnread(Utils::getConnectedUserId());
        } catch (Throwable $e) {
            // Le compteur ne doit jamais empêcher l'affichage d'une page (par exemple la page d'erreur).
            return 0;
        }
    }

    /**
     * Construit le chemin vers la vue demandée.
     * @param string $viewName : le nom de la vue demandée.
     * @return string : le chemin vers la vue demandée.
     */
    private function buildViewPath(string $viewName): string
    {
        return TEMPLATE_VIEW_PATH . $viewName . '.php';
    }
}
