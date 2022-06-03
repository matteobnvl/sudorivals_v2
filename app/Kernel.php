<?php

namespace App;

use App\Request;
use App\Route;

class Kernel
{
	public static function init()
	{
		self::parseEnv();
		// Montrer les erreurs en mode debug
		$debug = env('APP_DEBUG');
		setErrorsDisplay($debug);
		$routes = Route::getRoutes();
		Request::parseRoutes($routes);
		

	}

	private static function parseEnv()
	{
		$file = file(__BASEPATH__ . '/.env');
		// Je dépile l'array $file
		foreach ($file as $row) {
    		// Je filtre les lignes vides
    		if (strlen($row) > 0) {
        		// Je cherche la position de la première occurence du caractère "="
        		$pos = strpos($row, '=');
        		// Je découpe l'index et je le trim
        		$index = substr($row, 0, $pos);
        		$index = trim($index);
        		// Je découpe la value et je la trim
        		$value = substr($row, $pos + 1);
        		$value = trim($value);
       		 	// Je vérifie si $value est bien une string et non un caractère spécial indésirable
       		 	if (isset($value[0])) {
            			// J'efface la première quote ou double-quote si elle existe
            			if ($value[0] === '"' || $value[0] === "'") {
                			$value = substr($value, 1);
            			}
            			// J'efface la dernière quote ou double-quote si elle existe
            			if ($value[strlen($value) - 1] === '"' || $value[strlen($value) - 1] === "'") {
               				$value = substr($value, 0, -1);
            			}
            			$_ENV[$index] = $value;
        		}
    		}		
		}		
	}
}