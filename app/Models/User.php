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
        $qry = "SELECT id, firstname, lastname, email, created_at, updated_at FROM users WHERE email = :email AND password = :password";
        $stt = $db->prepare($qry);
        $stt->execute([
            ':email' => $email,
            ':password' => md5($pwd)
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
}


