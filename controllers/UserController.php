<?php

/**
 * Contrôleur des membres : inscription, connexion, déconnexion, profil public et page "Mon compte".
 */
class UserController
{
    /**
     * Affiche le formulaire d'inscription.
     * @return void
     */
    public function showRegisterForm(): void
    {
        // Un membre déjà connecté n'a rien à faire sur cette page.
        if (Utils::isConnected()) {
            Utils::redirect('myAccount');
        }

        $view = new View('Inscription');
        $view->render('registerForm', ['errors' => [], 'pseudo' => '', 'email' => '']);
    }

    /**
     * Traite le formulaire d'inscription : vérifie les données, crée le membre puis le connecte.
     * @return void
     */
    public function register(): void
    {
        Utils::checkCsrfToken();

        $pseudo = trim(Utils::request('pseudo', ''));
        $email = trim(Utils::request('email', ''));
        $password = Utils::request('password', '');

        $errors = $this->validateUserData($pseudo, $email, $password, true);

        // S'il y a des erreurs, on réaffiche le formulaire avec les messages et les valeurs déjà saisies.
        if (!empty($errors)) {
            $view = new View('Inscription');
            $view->render('registerForm', ['errors' => $errors, 'pseudo' => $pseudo, 'email' => $email]);
            return;
        }

        // Tout est bon : on crée le membre avec un mot de passe hashé.
        $user = new User([
            'pseudo' => $pseudo,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);
        $userManager = new UserManager();
        $userId = $userManager->createUser($user);

        // On connecte directement le nouveau membre.
        $this->logUserIn($userId);
        Utils::addFlash('Bienvenue sur Tom Troc ! Ajoutez vos premiers livres à votre bibliothèque.');
        Utils::redirect('myAccount');
    }

    /**
     * Affiche le formulaire de connexion.
     * @return void
     */
    public function showLoginForm(): void
    {
        if (Utils::isConnected()) {
            Utils::redirect('myAccount');
        }

        $view = new View('Connexion');
        $view->render('loginForm', ['error' => null, 'email' => '']);
    }

    /**
     * Traite le formulaire de connexion.
     * @return void
     */
    public function login(): void
    {
        Utils::checkCsrfToken();

        $email = trim(Utils::request('email', ''));
        $password = Utils::request('password', '');

        $userManager = new UserManager();
        $user = $userManager->getUserByEmail($email);

        // password_verify() compare le mot de passe saisi avec le hash stocké en base.
        // On affiche le même message que l'email soit inconnu ou le mot de passe faux :
        // ainsi, on ne révèle pas quelles adresses email sont inscrites sur le site.
        if ($user === null || !password_verify($password, $user->getPassword())) {
            $view = new View('Connexion');
            $view->render('loginForm', ['error' => 'Adresse email ou mot de passe incorrect.', 'email' => $email]);
            return;
        }

        $this->logUserIn($user->getId());
        Utils::redirect('myAccount');
    }

    /**
     * Déconnecte le membre.
     * @return void
     */
    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        Utils::redirect('home');
    }

    /**
     * Affiche le profil public d'un membre et sa bibliothèque.
     * @return void
     */
    public function showProfile(): void
    {
        $id = (int) Utils::request('id', -1);

        $userManager = new UserManager();
        $user = $userManager->getUserById($id);

        if ($user === null) {
            throw new Exception("Ce membre n'existe pas.", 404);
        }

        $bookManager = new BookManager();
        $books = $bookManager->getBooksByUserId($id);

        $view = new View($user->getPseudo());
        $view->render('profile', ['user' => $user, 'books' => $books]);
    }

    /**
     * Affiche la page "Mon compte" : informations personnelles et bibliothèque du membre connecté.
     * @return void
     */
    public function showMyAccount(): void
    {
        $user = $this->getConnectedUser();
        $this->renderMyAccount($user, [], $user->getPseudo(), $user->getEmail());
    }

    /**
     * Traite le formulaire "Vos informations personnelles".
     * @return void
     */
    public function updateAccount(): void
    {
        $user = $this->getConnectedUser();
        Utils::checkCsrfToken();

        $pseudo = trim(Utils::request('pseudo', ''));
        $email = trim(Utils::request('email', ''));
        $password = Utils::request('password', '');

        // Le mot de passe est facultatif ici : s'il est vide, on garde l'ancien.
        $errors = $this->validateUserData($pseudo, $email, $password, false, $user->getId());

        if (!empty($errors)) {
            $this->renderMyAccount($user, $errors, $pseudo, $email);
            return;
        }

        $user->setPseudo($pseudo);
        $user->setEmail($email);
        if ($password !== '') {
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        }
        $userManager = new UserManager();
        $userManager->updateUser($user);

        Utils::addFlash('Vos informations ont été enregistrées.');
        Utils::redirect('myAccount');
    }

    /**
     * Change la photo de profil du membre connecté.
     * @return void
     */
    public function updateAvatar(): void
    {
        $userId = Utils::checkIfUserIsConnected();
        Utils::checkCsrfToken();

        try {
            $avatar = ImageUploader::upload('avatar', AVATAR_PATH);
            if ($avatar === null) {
                Utils::addFlash('Aucune image sélectionnée.', 'error');
            } else {
                $userManager = new UserManager();
                $userManager->updateAvatar($userId, $avatar);
                Utils::addFlash('Votre photo de profil a été modifiée.');
            }
        } catch (Exception $e) {
            // Les erreurs d'envoi (mauvais format, trop lourd...) sont affichées au membre.
            Utils::addFlash($e->getMessage(), 'error');
        }

        Utils::redirect('myAccount');
    }

    /**
     * Récupère le membre connecté (et redirige vers la connexion si personne ne l'est).
     * @return User
     */
    private function getConnectedUser(): User
    {
        $userManager = new UserManager();
        $user = $userManager->getUserById(Utils::checkIfUserIsConnected());

        // Cas rare : le compte a été supprimé alors que la session était encore ouverte.
        if ($user === null) {
            $_SESSION = [];
            Utils::redirect('loginForm');
        }
        return $user;
    }

    /**
     * Affiche la page "Mon compte".
     * @param User $user : le membre connecté.
     * @param array $errors : les erreurs du formulaire.
     * @param string $pseudo : le pseudo à afficher dans le formulaire.
     * @param string $email : l'email à afficher dans le formulaire.
     * @return void
     */
    private function renderMyAccount(User $user, array $errors, string $pseudo, string $email): void
    {
        $bookManager = new BookManager();
        $books = $bookManager->getBooksByUserId($user->getId());

        $view = new View('Mon compte');
        $view->render('myAccount', [
            'user' => $user,
            'books' => $books,
            'errors' => $errors,
            'pseudo' => $pseudo,
            'email' => $email,
        ]);
    }

    /**
     * Vérifie les données d'un membre (inscription ou modification du profil).
     * On vérifie tout côté serveur, même si le formulaire HTML a des "required" :
     * un utilisateur malveillant peut très bien envoyer une requête sans passer par notre formulaire.
     * @param string $pseudo
     * @param string $email
     * @param string $password
     * @param bool $isPasswordRequired : true à l'inscription, false sur "Mon compte" (vide = inchangé).
     * @param int $userId : le membre modifié (-1 pour une inscription), ignoré dans les tests d'unicité.
     * @return array : la liste des erreurs (vide si tout est correct).
     */
    private function validateUserData(string $pseudo, string $email, string $password, bool $isPasswordRequired, int $userId = -1): array
    {
        $errors = [];
        $userManager = new UserManager();

        if ($pseudo === '' || $email === '' || ($isPasswordRequired && $password === '')) {
            return ['Tous les champs sont obligatoires.'];
        }

        if (mb_strlen($pseudo) < 3 || mb_strlen($pseudo) > 50) {
            $errors[] = 'Le pseudo doit contenir entre 3 et 50 caractères.';
        } elseif ($userManager->isPseudoTaken($pseudo, $userId)) {
            $errors[] = 'Ce pseudo est déjà utilisé.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email n'est pas valide.";
        } elseif ($userManager->isEmailTaken($email, $userId)) {
            $errors[] = 'Un compte existe déjà avec cette adresse email.';
        }

        if ($password !== '' && mb_strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }

        return $errors;
    }

    /**
     * Enregistre le membre connecté dans la session.
     * @param int $userId
     * @return void
     */
    private function logUserIn(int $userId): void
    {
        // On change l'identifiant de session à la connexion (protection contre le "vol de session").
        session_regenerate_id(true);
        $_SESSION['userId'] = $userId;
    }
}
