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
     * Affiche la liste des articles / produits pour la page d'accueil
     *
     * @throws Exception
     */
    public function ProductsAction()
    {
        $query = $_GET['sort'];

        $articles = Articles::getAll($query);

        header('Content-Type: application/json');
        echo json_encode($articles);
    }

    /**
     * Recherche dans la liste des villes
     *
     * @throws Exception
     */
    public function CitiesAction(){

        $cities = Cities::search($_GET['query']);

        header('Content-Type: application/json');
        echo json_encode($cities);
    }

    /**
     * RÉCUPÉRATION DES STATS
     * @throws Exception
     */
    public function StatsAction()
    {
        // Récupère les stats avec la méthode "fetch" de la classe "Stats"
        $stats = Stats::fetch();

        // Envoie la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($stats);
    }
}
