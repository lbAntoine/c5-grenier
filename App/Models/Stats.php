<?php

namespace App\Models;

use App\Utility\Hash;
use Core\Model;
use App\Core;
use Exception;
use App\Utility;

use OpenApi\Annotations as OA;

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

    /**
     * @OA\Schema(
     *  schema="PublicationsDates",
     *  description="Pairs of values composed of a date on which at least one article was submitted, and the number of submission on that day",
     *  @OA\Property(type="string", format="date", property="date"),
     *  @OA\Property(type="string", property="count")
     * )
     */
    public static function fetchPublicationsDatesStats() {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT published_date AS "date", COUNT(*) AS count FROM `articles` GROUP BY published_date;');

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
