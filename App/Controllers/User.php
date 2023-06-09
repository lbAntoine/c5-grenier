<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Models\Cities;
use App\Utility\Hash;
use App\Utility\Mailer;
use Core\View;
use Exception;

/**
 * User controller
 */
class User extends \Core\Controller
{

    /**
     * AFFICHE LA PAGE DE CONNEXION
     */
    public function loginAction()
    {
        if(isset($_POST['submit'])){

            $f = array_map('htmlspecialchars', $_POST);

            // Appelle la méthode "login" pour tenter la connexion de l'utilisateur
            $connect = $this->login($f);
            if ($connect){
                // Si la connexion est réussie, redirige l'utilisateur vers son compte
                header('Location: /account');
            }

        }

        // Affiche la page de connexion en appelant la méthode "renderTemplate" de la classe "View"
        View::renderTemplate('User/login.html.twig');
    }

    /**
     * AFFICHE LA PAGE DE CONNEXION
     */
    public function forgotAction()
    {
        try {
            if(isset($_POST['submit'])){
                $f = array_map('htmlspecialchars', $_POST);
                if(!isset($f['emailRecuperation']) || (filter_var($f['emailRecuperation'], FILTER_VALIDATE_EMAIL) === false)){
                    $_SESSION['flash'] = ["message" => "Il faut renseigner une adresse mail valide pour la récupération!",
                        "type" => "danger"];

                } else {
                    $user = \App\Models\User::getByLogin($f['emailRecuperation']);
                    if ($user == false) {
                        $_SESSION['flash'] = ["message" => "l'adresse mail ne correspond à aucun compte.",
                            "type" => "danger"];
                    } else {
                        $bytes = random_bytes(20);
                        $token = bin2hex($bytes);
                        $user['token'] = $token;
                        \App\Models\User::save($user);

                        // send email with the link and the token
                        $link = "http://".$_SERVER['SERVER_NAME'].':8080/recuperation/'.$token;

                        $subject = 'Reinitialisation de mot de passe - VideGrenierEnLigne';
                        $body = View::renderTemplateForMail('User/lien_changement_mdp.html.twig', ["nom" => $user['username'],
                            "link" => $link]);

                        $mailer = new Mailer();
                        $result = $mailer->SendMail($subject, $body, $user['email']);
                        if ($result === true) {
                            $_SESSION['flash'] = ["message" => "Un email de récuperation vous a été envoyé.",
                                "type" => "success"];
                        } else {
                            $_SESSION['flash'] = ["message" => $result,
                                "type" => "danger"];
                        }
                    }
                }
            }

        } catch (Exception $e) {
            var_dump($e);
        }
        // Affiche la page de connexion en appelant la méthode "renderTemplate" de la classe "View"
        header('Location: /login');
    }

    /**
     * Affiche la page pour le changement de mot de passe
     */
    public function recuperationAction()
    {
        try {
            $token = $this->route_params['token'];
            if (!isset($token) || trim($token) === '') {
                return 'Invalid token';
            }

            $user = \App\Models\User::getByToken($token);
            if (!isset($user) || $user === false) {
                View::renderTemplate('404.html.twig');
            }

            if (isset($_POST['submit'])) {
                $f = array_map('htmlspecialchars', $_POST); // --> prevent from XSS attack


                if (empty($f['password'])) {
                    $_SESSION['flash'] = ["message" => "Veuillez remplir le mot de passe.",
                        "type" => "danger"];

                } elseif (empty($f['password-check'])) {
                    $_SESSION['flash'] = ["message" =>"Veuillez remplir le mot de passe de confirmation.",
                        "type" => "danger"];
                }

                if ($f['password'] !== $f['password-check']) {
                    $_SESSION['flash'] = ["message" => "Les mots de passe ne correspondent pas.",
                        "type" => "danger"];
                }

                $salt = Hash::generateSalt(32);

                $result = \App\Models\User::changePassword($user['email'], $salt, Hash::generate($f['password'], $salt));
                if ($result === true) {
                    $_SESSION['flash'] = ["message" => "Le mot de passe a bien été modifié",
                        "type" => "success"];
                    View::renderTemplate('User/login.html.twig');
                    return true;
                } else {
                    $_SESSION['flash'] = ["message" => "Il y a eu un problème dans le changement de votre mot de passe.",
                        "type" => "danger"];
                }
            }

            View::renderTemplate('User/resetpassword.html.twig');
        } catch (Exception $e) {
            var_dump($e);
        }

    }


    /**
     * AFFICHE LA PAGE DE CREATION DE COMPTE
     */
    public function registerAction()
    {
        // Récupère les villes dans la base de données
        $villes = Cities::getAllVilles();

        if(isset($_POST['submit'])){
            $f = $_POST;
            $user = \App\Models\User::getByLogin($f['email']);

            // Message Erreur : Adresse mail déjà utilisée
            if ($user) {
                $_SESSION['flash'] = ["message" => "Un compte existe déjà pour cette adresse mail !",
                    "type" => "danger"];
                View::renderTemplate('User/register.html.twig');
                return null;
            }

            // Message Erreur : Conformiter mot de passe
            if(strlen($f['password']) < 8 || preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]|[A-Z]|[0-9]/', $f['password']) != 1){
                $_SESSION['flash'] = ["message" => "Le mot doit contenir au minimum 8 caractères, avec une majuscule, un chiffre et un caractère spécial (!@$%)",
                    "type" => "danger"];
                View::renderTemplate('User/register.html.twig');
                return null;
            }

            // Message Erreur : Mots de passe différents
            if($f['password'] !== $f['password-check']){
                $_SESSION['flash'] = ["message" => "Les mots de passes ne correspondent pas !",
                    "type" => "danger"];
                View::renderTemplate('User/register.html.twig');
                return null;
            }
            // Appelle la méthode "register" pour créer le compte utilisateur avec les données du formulaire
            $id = $this->register($f);
            if ($id != null) {
                $this->login(['email' => $f['email'],
                    'password' => $f['password']]
                );
            }

            // Rappeler la méthode "login" pour connecter l'utilisateur automatiquement
            header('Location: /account');
        }

        // Affiche la page de création de compte en appelant la méthode "renderTemplate" de la classe "View"
        View::renderTemplate('User/register.html.twig', [
            'villes' => $villes
        ]);
    }

    /**
     * AFFICHE LA PAGE DU COMPTE UTILISATEUR
     */
    public function accountAction()
    {
        // Récupère les articles créés par l'utilisateur connecté
        try {
            $articles = Articles::getByUser($_SESSION['user']['id']);
        }catch (Exception $e){
            var_dump($e);
        }

        // Affiche la page du compte en appelant la méthode "renderTemplate" de la classe "View"
        View::renderTemplate('User/account.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * FONCTION PRIVEE POUR ENREGISTRER UN UTILISATEUR
     * @param array $data Les données du formulaire d'inscription.
     */
    private function register($data)
    {
        try {
            // Génère un salt, qui sera utilisé pendant le processus de hachage du mot de passe
            $salt = Hash::generateSalt(32);

            // Crée un nouvel utilisateur dans la base de données en utilisant les données du formulaire et le salt généré
            $userID = \App\Models\User::createUser([
                "email" => $data['email'],
                "username" => $data['username'],
                "password" => Hash::generate($data['password'], $salt),
                "salt" => $salt,
                "city" => $data['city']
            ]);

            // Retourne l'ID de l'utilisateur créé
            return $userID;

        } catch (Exception $ex) {
            // Si une erreur se produit, définir un flash pour afficher le message d'erreur à l'utilisateur
            /* Utility\Flash::danger($ex->getMessage()); */
            $_SESSION['flash'] = ["message" => "Une erreur est survenu durant l'inscription!",
                "type" => "danger"];
        }
    }

    /**
     * FONCTION PRIVEE POUR CONNERCTER UN UTILISATEUR
     * @param array $data Les données du formulaire de connexion.
     * @return bool True si la connexion est réussie, false sinon.
     */
    private function login($data){
        try {
            if(!isset($data['email']) || (filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false)){
                $_SESSION['flash'] = ["message" => "Il faut renseigner une adresse mail valide!",
                    "type" => "danger"];
                return false;
            }

            // Récupère l'utilisateur correspondant à l'adresse email fournie.
            $user = \App\Models\User::getByLogin($data['email']);

            if ($user == null) {
                $_SESSION['flash'] = ["message" => "L'email rentré n'est pas attribué à un compte.",
                    "type" => "danger"];
                return false;
            }
            // Vérifie si le mot de passe fourni correspond au mot de passe haché stocké dans la base de données.
            if (Hash::generate($data['password'], $user['salt']) !== $user['password']) {
                $_SESSION['flash'] = ["message" => "Le mot de passe ou l'email ne correspond pas !",
                    "type" => "danger"];
                return false;
            }

            // Créer un cookie de "remember me" si l'utilisateur a sélectionné cette option sur le formulaire de connexion.
            if (key_exists('remember', $data)){
                if ($data['remember'] == "on"){
                    $_SESSION['user_is_loggedin'] = 1;
                    $cookiehash = sha1($user["username"] . $user["id"]);
                    setcookie('uname', $cookiehash, time()+3600*24*365,'/');
                    $user['cookie_session'] = $cookiehash;
                    \App\Models\User::save($user);
                }
            }

            // Stocke les informations de l'utilisateur dans la session.
            $_SESSION['user'] = array(
                'id' => $user['id'],
                'username' => $user['username'],
                'admin' => $user['is_admin']
            );

            return true;

        } catch (Exception $ex) {
            $_SESSION['flash'] = ["message" => "Une erreur est survenu durant la connection!",
                "type" => "danger"];
            return false;
        }
    }


    /**
     * LOGOUT : SUPPRIME LE COOKIE ET LA SESSION
     * Renvoie true si tout va bien, sinon renvoie false si tout va mal.
     * @access public
     * @return boolean
     * @since 1.0.2
     */
    public function logoutAction() {

        if (isset($_COOKIE['uname'])){
            //  Supprimer le cookie de rappel de l'utilisateur s'il a été stocké.
            $user = \App\Models\User::getByCookie($_COOKIE['uname']);
            $user['cookie_session'] = null;
            \App\Models\User::save($user);
            unset($_COOKIE['uname']);
            setcookie('uname', null, -1, '/');

        }

        // Supprime toutes les données enregistrées dans la session.
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        // Si la déconnexion est réussie, redirige l'utilisateur vers la page d'accueil
        header ("Location: /");

        return true;
    }

}
