<?php

namespace App\Controllers;

use App\Model\UserRegister;
use App\Models\Articles;
use App\Utility\Flash;
use App\Utility\Hash;
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
            $f = $_POST;

            // TODO: Validation

            // Appelle la méthode "login" pour tenter la connexion de l'utilisateur
            $this->login($f);

            // Si la connexion est réussie, redirige l'utilisateur vers son compte
            header('Location: /account');
        }

        // Affiche la page de connexion en appelant la méthode "renderTemplate" de la classe "View"
        View::renderTemplate('User/login.html');
    }

    /**
     * AFFICHE LA PAGE DE CREATION DE COMPTE
     */
    public function registerAction()
    {
        if(isset($_POST['submit'])){
            $f = $_POST;

            $user = \App\Models\User::getByLogin($f['email']);

            if ($user) {
                $_SESSION['flash'] = "Un compte existe déjà pour cette adresse mail !";
                View::renderTemplate('User/register.html.twig');
                return null;
            }

            if(strlen($f['password']) < 8 || preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]|[A-Z]|[0-9]/', $f['password']) != 1){
                $_SESSION['flash'] = "Le mot doit contenir au minimum 8 caractères, avec une majuscule, un chiffre et un caractère spécial (!@$%)";
                View::renderTemplate('User/register.html.twig');
                return null;
            }

          // git flow finish feature/CG13-form-inscription

            if($f['password'] !== $f['password-check']){
                $_SESSION['flash'] = "Les mots de passes ne correspondent pas !";
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
            header('Location: /account');
        }

        // Affiche la page de création de compte en appelant la méthode "renderTemplate" de la classe "View"
         View::renderTemplate('User/register.html.twig');
        unset($_SESSION['flash']);
    }

    /**
     * AFFICHE LA PAGE DU COMPTE UTILISATEUR
     */
    public function accountAction()
    {
        // Récupère les articles créés par l'utilisateur connecté
        $articles = Articles::getByUser($_SESSION['user']['id']);

        // Affiche la page du compte en appelant la méthode "renderTemplate" de la classe "View"
        View::renderTemplate('User/account.html', [
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
                "salt" => $salt
            ]);

            // Retourne l'ID de l'utilisateur créé
            return $userID;

        } catch (Exception $ex) {
            // TODO: Si une erreur se produit, définir un flash pour afficher le message d'erreur à l'utilisateur
            /* Utility\Flash::danger($ex->getMessage()); */
            $_SESSION['flash'] = $ex->getMessage();
        }
    }

    /**
     * FONCTION PRIVEE POUR CONNERCTER UN UTILISATEUR
     * @param array $data Les données du formulaire de connexion.
     * @return bool True si la connexion est réussie, false sinon.
     */
    private function login($data){
        try {
            if(!isset($data['email'])){
                throw new Exception('TODO');
            }

            // Récupère l'utilisateur correspondant à l'adresse email fournie.
            $user = \App\Models\User::getByLogin($data['email']);

            // Vérifie si le mot de passe fourni correspond au mot de passe haché stocké dans la base de données.
            if (Hash::generate($data['password'], $user['salt']) !== $user['password']) {
                return false;
            }

            // TODO: Créer un cookie de "remember me" si l'utilisateur a sélectionné cette option sur le formulaire de connexion.
            // https://github.com/andrewdyer/php-mvc-register-login/blob/development/www/app/Model/UserLogin.php#L86

            // Stocke les informations de l'utilisateur dans la session.
            $_SESSION['user'] = array(
                'id' => $user['id'],
                'username' => $user['username'],
            );

            return true;

        } catch (Exception $ex) {
            // TODO: Définir un message d'erreur pour l'affichage à l'utilisateur.
            /* Utility\Flash::danger($ex->getMessage());*/
            $_SESSION['flash'] = "Une erreur est survenu pendant votre connection !";
        }
    }


    /**
     * LOGOUT : SUPPRIME LE COOKIE ET LA SESSION
     * Renvoie true si tout va bien, sinon renvoie false.
     * @access public
     * @return boolean
     * @since 1.0.2
     */
    public function logoutAction() {

        /*
        if (isset($_COOKIE[$cookie])){
            // TODO: Supprimer le cookie de rappel de l'utilisateur s'il a été stocké.
            // https://github.com/andrewdyer/php-mvc-register-login/blob/development/www/app/Model/UserLogin.php#L148
        }*/
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

        header ("Location: /");

        return true;
    }

}
