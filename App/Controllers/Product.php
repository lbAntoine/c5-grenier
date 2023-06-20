<?php

namespace App\Controllers;

use App\Models\Articles;
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

                // TODO: Validation des données soumises

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
        View::renderTemplate('Product/Add.html');
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
        } catch(\Exception $e){
            // Affiche l'exception levée avec la fonction "var_dump"
            var_dump($e);
        }

        // Affiche la page du produit en appelant la méthode "renderTemplate" de la classe "View"
        View::renderTemplate('Product/Show.html', [
            'article' => $article[0],
            'suggestions' => $suggestions
        ]);
    }
}
