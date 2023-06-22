<?php

namespace App\Models;

use App\Utility\Hash;
use Core\Model;
use App\Core;
use Exception;
use App\Utility;

/**
 * City Model:
 */
class Cities extends Model {

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function getAllVilles() {
        $db = static::getDB();

        $query = 'SELECT ville_id, ville_nom_reel FROM villes_france';

        $query .= ' WHERE ville_departement LIKE "%%"' ;

        $query .= ' ORDER BY ville_nom_reel ASC';
                        
        $stmt = $db->query($query);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */    
    public static function search($str) {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT ville_nom_reel FROM villes_france WHERE ville_nom_reel LIKE :query');

        $query = $str . '%';

        $stmt->bindParam(':query', $query);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
    }
}
