<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Models\Cities;
use App\Models\Stats;
use \Core\View;
use Exception;

use OpenApi\Annotations as OA;

/**
 * API controller
 * 
 * @OA\Info(title="API Vide grenier en ligne", version="1.0")
 * @OA\Server(
 *  url="http://localhost:8080/api",
 *  description="API du site 'Vide grenier en ligne'."
 * )
 */
class Api extends \Core\Controller
{

    /**
     * AFFICHE LA LISTE DES ARTICLES ET PRODUITS POUR LA PAGE D'ACCUEIL
     * @throws Exception
     * 
     * @OA\Get(
     *  path="/products",
     *  @OA\Parameter(
     *      name="sort",
     *      in="query",
     *      description="the order in which the articles should be displayed",
     *      @OA\Schema(
     *          type="string",
     *          enum={
     *              "date",
     *              "views"
     *          }
     *      ),
     *      required=true
     *  ),
     *  @OA\Parameter(
     *      name="recherche",
     *      in="query",
     *      description="the name of an article or a substring of it",
     *      @OA\Schema(
     *          type="string"
     *      ),
     *      required=true
     *  ),
     *  @OA\Response(
     *      response="200",
     *      description="Products response",
     *      @OA\JsonContent(
     *          type="array",
     *          @OA\Items(ref="#/components/schemas/Article")
     *      )
     *  )
     * )
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
     * AFFICHE LA LISTE DES ARTICLES ET PRODUITS AUTOUR DE MOI POUR LA PAGE D'ACCUEIL
     * @throws Exception
     */
    public function AutourAction()
    {
        // Récupère le numéro id l'utilisateur connecté
        $id_user = $_GET['id_user'];

        // Récupère tous les articles avec la méthode "getAll" de la classe "Articles"
        $articles = Articles::getAutour($id_user);

        // Envoie la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($articles);
    }

    /**
     * RECHERCHE DANS LA LISTE DES VILLES
     * @throws Exception
     * 
     * @OA\Get(
     *  path="/cities",
     *  @OA\Parameter(
     *      name="query",
     *      in="query",
     *      description="A city's real name or its begining",
     *      @OA\Schema(
     *          type="string",
     *      ),
     *      required=true
     *  ),
     *  @OA\Response(
     *      response="200",
     *      description="Cities response",
     *      @OA\JsonContent(
     *          type="array",
     *          @OA\Items(type="string")
     *      )
     *  )
     * )
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
     * RÉCUPÈRE LES STATS
     * @throws Exception
     * 
     * @OA\Get(
     *  path="/stats",
     *  @OA\Response(
     *      response="200",
     *      description="Stats response",
     *      @OA\JsonContent(ref="#/components/schemas/Stats")
     *  )
     * )
     */
    public function StatsAction()
    {
        // Effectue une recherche dans la liste des villes avec la méthode "search" de la classe "Stats"
        $stats = Stats::fetch();
        // Envoie la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($stats);
    }

    /**
     * RECHERCHE DES STATISTIQUES EN LIEN AVEC LES UTILISATEURS
     * @throws Exception
     */
    public function StatsUsersAction()
    {
        // Effectue une recherche dans la liste des villes avec la méthode "search" de la classe "Stats"
        $stats = Stats::fetchStatsUsers();

        // Envoie la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($stats);
    }

    /**
     * RECHERCHE DES STATISTIQUES EN LIEN AVEC LES ARTICLES
     * @throws Exception
     */
    public function StatsArticlesAction()
    {
        // Effectue une recherche dans la liste des villes avec la méthode "search" de la classe "Stats"
        $stats = Stats::fetchStatsArticles();

        // Envoie la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($stats);
    }

    /**
     * RECHERCHE DES STATISTIQUES EN LIEN AVEC LES ARTICLES
     * @throws Exception
     */
    public function StatsVuesAction()
    {
        // Effectue une recherche dans la liste des villes avec la méthode "search" de la classe "Stats"
        $stats = Stats::fetchStatsVues();

        // Envoie la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($stats);
    }

    /**
     * RECHERCHE DES STATISTIQUES EN FORME DE GRAPH
     * @throws Exception
     */
    public function StatsGraphAction()
    {
        // Effectue une recherche dans la liste des villes avec la méthode "search" de la classe "Stats"
        $stats = Stats::fetchStatsGraph();

        // Envoie la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($stats);
    }
}
