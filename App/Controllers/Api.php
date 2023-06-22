<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Models\Cities;
use App\Models\Stats;
use \Core\View;
use Exception;

/**
 * API controller
 */
class Api extends \Core\Controller
{

    /**
     * AFFICHE LA LISTE DES ARTICLES ET PRODUITS POUR LA PAGE D'ACCUEIL
     * @throws Exception
     */
    public function ProductsAction()
    {
        // Récupère la valeur du paramètre "sort" dans la requête GET
        $query = $_GET['sort'];

        // Récupère la valeur du paramètre "recherche" dans la requête GET
        $recherche = $_GET['recherche'];

        // Récupère tous les articles avec la méthode "getAll" de la classe "Articles"
        $articles = Articles::getAll($query,$recherche);

        // Envoie la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($articles);
    }

    /**
     * RECHERCHE DANS LA LISTE DES VILLES
     * @throws Exception
     */
    public function CitiesAction()
    {
        // Effectue une recherche dans la liste des villes avec la méthode "search" de la classe "Cities"
        $cities = Cities::search($_GET['query']);

        // Envoie la réponse au format JSON
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($cities);
    }

    /**
     * RECHERCHE DANS LA LISTE DES VILLES
     * @throws Exception
     */
    public function StatsAction()
    {
        // Effectue une recherche dans la liste des villes avec la méthode "search" de la classe "Stats"
        $stats = Stats::fetch();
        // Envoie la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($stats);
    }
}
