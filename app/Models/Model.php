<?php

namespace App\Models;

class Model
{
    protected $db;

    public function __construct()
    {
        $host = env('DB_HOST');
        $dbuser = env('DB_USER');
        $dbpwd = env('DB_PASSWORD');
        $dbname = env('DB_NAME');
        $pdoReqArg1 = "mysql:host=". $host .";dbname=". $dbname .";";
        $pdoReqArg2 = $dbuser;
        $pdoReqArg3 = $dbpwd;
        
        try {

            $this->db = new \PDO($pdoReqArg1, $pdoReqArg2, $pdoReqArg3);
            $this->db->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE , \PDO::ERRMODE_EXCEPTION);
            $this->db->exec("SET NAMES 'utf8'");

        } catch(\PDOException $e) {

            $errorMessage = $e->getMessage();
            echo $errorMessage;
        }
    }
}