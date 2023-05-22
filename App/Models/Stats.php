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
        $db = static::getDB();

        $stmt = $db->prepare('SELECT COUNT(*) AS articlesNumber, SUM(views) AS totalViews, AVG(views) AS averageViews, COUNT(DISTINCT user_id) AS authorsNumber FROM `articles`;');

        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
