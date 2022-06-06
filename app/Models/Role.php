<?php

namespace App\Models;

use App\Models\Model;

class Role extends Model
{
    public static function selectAll()
    {
        $db = self::db();
        $qry = "SELECT * FROM roles";
        $stt = $db->prepare($qry);
        $stt->execute();
        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function createRole($post){
        $db = self::db();
        $qry = "INSERT INTO roles (nom_role)
        VALUES (:nom_role)";
        $stt = $db->prepare($qry);
        $stt->execute([
            ':nom_role'=>$post['nom_role']
        ]);
    }
}



