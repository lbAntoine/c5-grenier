<?php

namespace App\Models;

use Core\Model;
use App\Core;
use DateTime;
use Exception;
use App\Utility;

use OpenApi\Annotations as OA;

/**
 * Articles Model
 * 
 * @OA\Schema(
 *  schema="Article",
 *  description="Article available to be given",
 *  @OA\Property(type="string", property="id", description="ID of the article in the database"),
 *  @OA\Property(type="string", property="name", description="Name of the article"),
 *  @OA\Property(type="string", property="description"),
 *  @OA\Property(type="string", format="date", property="published_date"),
 *  @OA\Property(type="string", property="user_id"),
 *  @OA\Property(type="string", property="views"),
 *  @OA\Property(type="string", property="picture")
 *  )
 * )
 */
class Articles extends Model {

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function getAll($filter,$recherche) {
        $db = static::getDB();

        $query = 'SELECT * FROM articles ';

        // Requête permettant de rechercher des articles selon la barre de recherche
        if($recherche != '') {
            $query .= ' WHERE articles.name LIKE "%'.$recherche.'%" OR articles.description LIKE "%'.$recherche.'%"' ;
        }
        
        switch ($filter){
            case 'views':
                $query .= ' ORDER BY articles.views DESC';
                break;
            case 'date':
                $query .= ' ORDER BY articles.published_date DESC';
                break;
            case '':
                break;
        }
        
        $stmt = $db->query($query);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function getAutour($id_user) {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT * FROM articles
            INNER JOIN users ON articles.lieu = users.city
            WHERE users.id = ? ');

        $stmt->execute([$id_user]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function getOne($id) {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT * FROM articles
            INNER JOIN users ON articles.user_id = users.id
            WHERE articles.id = ? 
            LIMIT 1');

        $stmt->execute([$id]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function addOneView($id) {
        $db = static::getDB();

        $stmt = $db->prepare('
            UPDATE articles 
            SET articles.views = articles.views + 1
            WHERE articles.id = ?');

        $stmt->execute([$id]);
    }

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function getByUser($id) {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT *, articles.id as id FROM articles
            LEFT JOIN users ON articles.user_id = users.id
            WHERE articles.user_id = ?');

        $stmt->execute([$id]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function getSuggest() {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT *, articles.id as id FROM articles
            INNER JOIN users ON articles.user_id = users.id
            ORDER BY published_date DESC LIMIT 10');

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function save($data) {
        $db = static::getDB();

        $stmt = $db->prepare('INSERT INTO articles(name, description, lieu, user_id, published_date) VALUES (:name, :description, :lieu, :user_id,:published_date)');

        $published_date =  new DateTime();
        $published_date = $published_date->format('Y-m-d');
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':lieu', $data['lieu']);
        $stmt->bindParam(':published_date', $published_date);
        $stmt->bindParam(':user_id', $data['user_id']);

        $stmt->execute();

        return $db->lastInsertId();
    }

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function attachPicture($articleId, $pictureName){
        $db = static::getDB();

        $stmt = $db->prepare('UPDATE articles SET picture = :picture WHERE articles.id = :articleid');

        $stmt->bindParam(':picture', $pictureName);
        $stmt->bindParam(':articleid', $articleId);

        $stmt->execute();
    }

}
