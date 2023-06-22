<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Utility\Mailer;
use App\Utility\Upload;
use \Core\View;

/**
 * Product controller
 */
class Product extends \Core\Controller
{

    /**
     * AFFICHE LA PAGE POUR DEPOSER UNE ANNONCE 
     * @return void
     */
    public function indexAction()
    {
        // Vérifie si la méthode POST est utilisée pour soumettre les données du formulaire
        if(isset($_POST['submit'])) {

            try {
                // Stocke les données soumises dans la variable $f
                $f = $_POST;

                // Ajoute l'ID de l'utilisateur actuellement connecté aux données soumises
                $f['user_id'] = $_SESSION['user']['id'];

                // Appelle la méthode "save" de la classe "Articles" pour sauvegarder les données dans la base de données
                // L'ID du produit nouvellement créé est stocké dans la variable $id
                $id = Articles::save($f);

                // Appelle la méthode "uploadFile" de la classe "Upload" pour télécharger le fichier image soumis dans le formulaire
                // Le nom du fichier téléchargé est stocké dans la variable $pictureName
                $pictureName = Upload::uploadFile($_FILES['picture'], $id);

                // Appelle la méthode "attachPicture" de la classe "Articles" pour lier le nom du fichier image à l'ID du produit nouvellement créé dans la base de données
                Articles::attachPicture($id, $pictureName);

                // Redirige vers la page du produit nouvellement créé
                header('Location: /product/' . $id);
            } catch (\Exception $e){
                // Affiche l'exception levée avec la fonction "var_dump"
                var_dump($e);
            }
        }

        // Affiche le formulaire d'ajout d'un produit en appelant la méthode "renderTemplate" de la classe "View"
        View::renderTemplate('Product/Add.html.twig');
    }

    /**
     * AFFICHE LA PAGE D'UN PRODUIT
     * @return void
     */
    public function showAction()
    {
        // Récupère l'ID du produit à afficher depuis les paramètres de la route
        $id = $this->route_params['id'];
        try {
            // Incrémente le nombre de vues du produit avec l'ID récupéré
            Articles::addOneView($id);
            // Récupère des suggestions de produits similaires
            $suggestions = Articles::getSuggest();
            // Récupère les données du produit avec l'ID récupéré
            $article = Articles::getOne($id);
            if(isset($_POST['submit'])){
                $f = array_map('htmlspecialchars', $_POST);

                if ($f['name'] == null || $f['email'] == null || $f['subject'] == null || $f['comment'] == null) {
                    $_SESSION['flash'] = ["message" => "Vous devez remplir tout les champs du formulaires",
                                            "type" => "danger"];
                    View::renderTemplate('Product/Show.html.twig', [
                        'article' => $article[0],
                        'suggestions' => $suggestions
                    ]);
                }
                // Appelle la méthode "register" pour créer le compte utilisateur avec les données du formulaire
                $subject = 'Contact - VideGrenierEnLigne';
                $body = View::renderTemplateForMail('Product/mail_contact.html.twig', ["nom" => $f['name'],
                                                                                                 "subject" => $f["subject"],
                                                                                                 "comment" => $f["comment"],
                                                                                                 "email" => $f["email"]]);
                $user = \App\Models\User::getById($article[0]["user_id"]);
                $mailer = new Mailer();
                $result = $mailer->SendMail($subject, $body, $user['email']);
                $_SESSION['flash'] = ["message" => "Le mail a bien été envoyé !",
                                        "type" => "success"];

                $_POST = array();
            }
        } catch(\Exception $e){
            // Affiche l'exception levée avec la fonction "var_dump"
            var_dump($e);
        }

        // Affiche la page du produit en appelant la méthode "renderTemplate" de la classe "View"
        View::renderTemplate('Product/Show.html.twig', [
            'article' => $article[0],
            'suggestions' => $suggestions
        ]);

    }
}
