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

    public static function fetch() {
        $stats = Stats::fetchArticlesStats();
        $stats += Stats::fetchUsersStats();
        $stats['publicationsDates'] = Stats::fetchPublicationsDatesStats();
        return $stats;
    }

    public static function fetchArticlesStats() {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT COUNT(*) AS articlesNumber, SUM(views) AS totalViews, AVG(views) AS averageViews, COUNT(DISTINCT user_id) AS authorsNumber FROM `articles`;');

        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function fetchUsersStats() {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT COUNT(*) AS usersNumber, SUM(is_admin) AS adminsNumber FROM `users`;');

        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function fetchPublicationsDatesStats() {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT published_date AS "date", COUNT(*) AS count FROM `articles` GROUP BY published_date;');

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
