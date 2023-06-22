<?php

namespace App\Models;

use App\Utility\Hash;
use Core\Model;
use App\Core;
use Exception;
use App\Utility;

/**
 * Stats Model:
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
    public static function fetchStatsGraph() {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT EXTRACT(YEAR FROM published_date) AS Annees, EXTRACT(MONTH FROM published_date) AS Mois, COUNT(*) AS Nombre_Articles FROM articles GROUP BY EXTRACT(YEAR FROM published_date), EXTRACT(MONTH FROM published_date) ORDER BY Annees, Mois;');

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
    }
    
}
