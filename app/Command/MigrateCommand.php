<?php

namespace App\Command;

use App\Models\Model;
use PDOException;

class MigrateCommand extends Model
{
    public function execute($script)
    {
        if ($script != null) {
            $scriptPath = '../migrations/' . $script;
            if (file_exists($scriptPath)) {
                $sql = file_get_contents($scriptPath);
                try {
                    $this->db->exec($sql);
                    echo "Le script ". $script. " a été executé avec succes." . PHP_EOL;
                } catch (PDOException $e) {
                    echo "Erreur lors de l'exécution du script : " . $e->getMessage() . PHP_EOL;
                }
            } else {
                echo "Le script ". $script . " n'existe pas." . PHP_EOL;
            }
        } else {
            echo "Aucun script n'a été spécifié." . PHP_EOL;
        }
    }
}