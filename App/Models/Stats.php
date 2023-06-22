<?php

namespace App\Models;

use App\Utility\Hash;
use Core\Model;
use App\Core;
use Exception;
use App\Utility;

/**
 * Stats Model
 *
 * @OA\Schema(
 *  schema="Stats",
 *  description="Various stats computed from the database contents",
 *  @OA\Property(type="string", property="articlesNumber", description="The number of articles available"),
 *  @OA\Property(type="string", property="totalViews", description="The total number of views on all the articles"),
 *  @OA\Property(type="string", property="averageViews", description="The average number of views on all the articles"),
 *  @OA\Property(type="string", property="authorsNumber", description="The number of account with at least one submitted article"),
 *  @OA\Property(type="string", property="usersNumber", description="The number of registered accounts"),
 *  @OA\Property(type="string", property="adminsNumber", description="The number of account with the admin role"),
 *  @OA\Property(type="array", @OA\Items(ref="#/components/schemas/PublicationsDates"), property="publicationsDates", description="A list of all the dates with at least one submission and their submissions counts")
 * )
 */
class Stats extends Model {

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function fetchStatsUsers() {
        $stats = Stats::fetchUsersStats();
        return $stats;
    }

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function fetchUsersStats() {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT COUNT(*) AS usersNumber, SUM(is_admin) AS adminsNumber FROM `users`;');

        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }


    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function fetchStatsArticles() {
        $stats = Stats::fetchArticlesStats();
        return $stats;
    }

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function fetchArticlesStats() {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT COUNT(*) AS articlesNumber, COUNT(DISTINCT user_id) AS authorsNumber FROM `articles`;');

        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }


    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function fetchStatsVues() {
        $stats = Stats::fetchVuesStats();
        return $stats;
    }

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function fetchVuesStats() {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT SUM(views) AS totalViews, AVG(views) AS averageViews FROM `articles`;');

        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }


    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    /**
     * @OA\Schema(
     *  schema="PublicationsDates",
     *  description="Pairs of values composed of a date on which at least one article was submitted, and the number of submission on that day",
     *  @OA\Property(type="string", format="date", property="date"),
     *  @OA\Property(type="string", property="count")
     * )
     */
    public static function fetchStatsGraph() {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT EXTRACT(YEAR FROM published_date) AS Annees, EXTRACT(MONTH FROM published_date) AS Mois, COUNT(*) AS Nombre_Articles FROM articles GROUP BY EXTRACT(YEAR FROM published_date), EXTRACT(MONTH FROM published_date) ORDER BY Annees, Mois;');

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

}