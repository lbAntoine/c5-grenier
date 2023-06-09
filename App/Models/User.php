<?php

namespace App\Models;

use App\Utility\Hash;
use Core\Model;
use App\Core;
use Exception;
use App\Utility;

/**
 * User Model:
 */
class User extends Model {

    /**
     * Crée un utilisateur
     */
    public static function createUser($data) {
        try {
            $db = static::getDB();

            $stmt = $db->prepare('INSERT INTO users(username, city, email, password, salt) VALUES (:username, :city, :email, :password,:salt)');
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':city', $data['city']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':password', $data['password']);
            $stmt->bindParam(':salt', $data['salt']);

            $stmt->execute();

            return $db->lastInsertId();
        }catch (Exception $e) {
            var_dump($e);
        }

    }

    public static function getByLogin($login)
    {
        $db = static::getDB();

        $stmt = $db->prepare("
            SELECT * FROM users WHERE ( users.email = :email) LIMIT 1
        ");

        $stmt->bindParam(':email', $login);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function getByCookie($cookie)
    {
        $db = static::getDB();

        $stmt = $db->prepare("
            SELECT * FROM users WHERE ( users.cookie_session = :cookie) LIMIT 1
        ");

        $stmt->bindParam(':cookie', $cookie);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function getByToken($token)
    {
        $db = static::getDB();

        $stmt = $db->prepare("
            SELECT * FROM users WHERE ( users.token = :token) LIMIT 1
        ");

        $stmt->bindParam(':token', $token);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function save($data){
        $db = static::getDB();
        $stmt = $db->prepare('UPDATE users
        SET username = :username, city = :city, email = :email, password = :password, salt= :salt, cookie_session = :cookie_session,
            token = :token
        WHERE id = :id');

        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':salt', $data['salt']);
        $stmt->bindParam(':cookie_session', $data['cookie_session']);
        $stmt->bindParam(':token', $data['token']);

        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function getById($userId)
    {
        try {
            $db = static::getDB();

            $stmt = $db->prepare("
            SELECT * FROM users WHERE ( users.id = :id) LIMIT 1
        ");

            $stmt->bindParam(':id', $userId);
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }catch (Exception $e) {
            var_dump($e);
        }
    }

    public static function changePassword($email, $passwordSalt, $passwordHash)
    {
        try {
            $db = static::getDB();

            $stmt = $db->prepare("
                UPDATE users SET 
                        password  = :password, 
                        salt = :salt, 
                        token = '' 
                WHERE ( users.email = :email)
            ");

            $stmt->bindParam(':password', $passwordHash);
            $stmt->bindParam(':salt', $passwordSalt);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            return $e;
        }
    }

}
