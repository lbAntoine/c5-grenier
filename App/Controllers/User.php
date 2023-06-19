<?php

namespace App\Controllers;

use App\Models\Articles;
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
                    $_SESSION['flash'] = "Il faut renseigner une adresse mail valide pour la récupération!";
                } else {
                    $user = \App\Models\User::getByLogin($f['emailRecuperation']);
                    if ($user == false) {
                        $_SESSION['flash'] = "l'adresse mail ne correspond à aucun compte.";
                    } else {
                        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

                        $payload = json_encode(['user_id' => $user['id']]);

                        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

                        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

                        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);

                        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

                        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

                        //json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $jwt)[1]))));
                        $user['token'] = $jwt;

                        \App\Models\User::save($user);

                        // send email with the link and the token
                        $link = "http://".$_SERVER['SERVER_NAME'].'/reset/'.$jwt;


                        $subject = 'Reinitialisation de mot de passe - VideGrenierEnLigne';
                        $body = View::renderTemplateForMail('User/lien_changement_mdp.html.twig', ["nom" => $user['name'],
                            "link" => $link]);

                        $mailer = new Mailer();
                        $result = $mailer->SendMail($subject, $body, $f['email']);
                        if ($result === true) {
                            $_SESSION['flash'] = "Un email de recuperation vous a ete envoyé.";
                        } else {
                            $_SESSION['flash'] = $result;
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
     * Affiche la page du compte
     */
    public function recuperationAction()
    {

        $token = $this->route_params['token'];
        if (!isset($token) || trim($token) === '') {
            return 'Invalid token';
        }

        $token = htmlspecialchars($token);

        $user = \App\Models\User::getByToken($token);
        if (!isset($user) || $user === false) {
             View::renderTemplate('404.html.twig');
        }

        if (isset($_POST['submit'])) {
            $f = array_map('htmlspecialchars', $_POST); // --> prevent from XSS attack


            if (empty($f['password'])) {
                $_SESSION['flash'] = "Veuillez remplir le mot de passe.";

            } elseif (empty($f['password-check'])) {
                $_SESSION['flash'] = "Veuillez remplir le mot de passe de confirmation.";
            }

            if ($f['password'] !== $f['password-check']) {
                $_SESSION['flash'] = "Les mots de passe ne correspondent pas.";
            }

            //check ok --> change password

            $result = $this->changePassword($user['email'], $f['password']);
            // return var_dump($result);
            if ($result === true) {
                $_SESSION['flash'] = "Le mot de passe a bien été modifié";
                View::renderTemplate('User/login.html');
                return true;
            } else {
                $_SESSION['flash'] = "Encore";
            }
        }

        View::renderTemplate('User/resetpassword.html');
    }


    /**
     * AFFICHE LA PAGE DE CREATION DE COMPTE
     */
    public function registerAction()
    {
        if(isset($_POST['submit'])){
            $f = array_map('htmlspecialchars', $_POST);

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
            /* Utility\Flash::danger($ex->getMessage()); */
            $_SESSION['flash'] = "Une erreur est survenu durant l'inscription!";
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
                $_SESSION['flash'] = "Il faut renseigner une adresse mail valide!";
                return false;
            }

            // Récupère l'utilisateur correspondant à l'adresse email fournie.
            $user = \App\Models\User::getByLogin($data['email']);
            // Vérifie si le mot de passe fourni correspond au mot de passe haché stocké dans la base de données.
            if (Hash::generate($data['password'], $user['salt']) !== $user['password']) {
                $_SESSION['flash'] = "Le mot de passe ou l'email ne correspond pas !";
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
            );

            return true;

        } catch (Exception $ex) {
            $_SESSION['flash'] = "Une erreur est survenu pendant votre connection !";
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

        header ("Location: /");

        return true;
    }

}
