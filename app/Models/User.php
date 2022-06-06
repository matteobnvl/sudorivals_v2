<?php

namespace App\Models;

use App\Models\Model;

class User extends Model
{
    public static function selectAll()
    {
        $db = self::db();
        $qry = "SELECT * FROM users";
        $stt = $db->prepare($qry);
        $stt->execute();
        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }





    public static function checkUser($email, $pwd) {

        $db = self::db();
        $qry = "SELECT id, firstname, lastname, email, created_at, updated_at, id_role, nom_role FROM users
        INNER JOIN roles ON users.id_role = roles.id_roles
         WHERE email = :email AND password = :password";
        $stt = $db->prepare($qry);
        $stt->execute([
            ':email' => $email,
            ':password' => $pwd
        ]);
        $user = $stt->fetch(\PDO::FETCH_ASSOC);

        if ($stt->rowCount() > 0) {

            foreach ($user as $key => $value) {
                $_SESSION[$key] = $value;
            }
            return true;
        }
        return false;
    }

    public static function selectUser($id){
        $db = self::db();
        $qry = "SELECT * FROM users 
                INNER JOIN roles on users.id_role = roles.id_roles
                WHERE users.id = :id";
        $stt = $db->prepare($qry);
        $stt->execute([
            ':id' => $id
        ]);
        return $stt->fetchAll(\PDO::FETCH_ASSOC);
        
    }

    public static function verifEmail($email,$pwd){
            $db = self::db();
            $qry = "SELECT id,`password` FROM users 
                    WHERE email = :email";
            $stt = $db->prepare($qry);
            $stt->execute([
                ':email' => $email
            ]);
            $result = $stt->fetchAll(\PDO::FETCH_ASSOC);
            
            if(empty($result)){
                return False;
            }else{
                if($result[0]['password'] == $pwd){
                    return False;
                }else{
                    return True;
                }
            }
    }

    public static function updateUserAdmin($post){
        $db = self::db();
        $qry = "UPDATE users
        SET firstname = :firstname, lastname = :lastname , email = :email, updated_at = :updated_at, id_role = :id_role
        WHERE id =:id";
        $stt = $db->prepare($qry);
        $stt->execute([
            ':firstname' => $post['firstname'],
            ':lastname' => $post['lastname'],
            ':email' => $post['email'],
            ':updated_at' => date('y-m-d'),
            ':id_role' => $post['id_role'],
            ':id' => $post['id']
        ]);

        if($post['id'] == $_SESSION['id']){
            User::updateSessionUser();
        }

        
    }

    public static function createUser($post){
        $db = self::db();
        $qry ="INSERT INTO users (firstname,lastname,email,`password`,created_at,updated_at,id_role,affichage) 
        VALUES (:firstname,:lastname,:email,:pwd,:created_at,:updated_at,:id_role,:affichage)";
        $stt = $db->prepare($qry);
        $stt->execute([
            ':firstname' => $post['firstname'],
            ':lastname' => $post['lastname'],
            ':email' => $post['email'],
            ':created_at' => date('y-m-d'),
            ':updated_at' => date('y-m-d'),
            ':id_role' => $post['id_role'],
            ':pwd' => $post['password'],
            ':affichage' => 'oui'
        ]);

    }

    public static function deleteUser($id){
        $db = self::db();
        $qry ="UPDATE users SET affichage =:affichage
        WHERE id = :id";
        $stt = $db->prepare($qry);
        $stt->execute([
            ':affichage' => 'non',
            ':id' => $id
        ]);
        return "L'utilisateur à bien été supprimé.";
    }

    public static function updateSessionUser(){
        $db = self::db();
        $qry = "SELECT id, firstname, lastname, email, created_at, updated_at, id_role, nom_role FROM users
        INNER JOIN roles ON users.id_role = roles.id_roles
         WHERE id = :id";
        $stt = $db->prepare($qry);
        $stt->execute([
            ':id' => $_SESSION['id']
        ]);
        $user = $stt->fetch(\PDO::FETCH_ASSOC);
        foreach ($user as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }
}


