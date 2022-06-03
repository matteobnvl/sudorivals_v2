<?php

namespace App;

class Route
{
    private static array $routes = [];

    public static function getRoutes()
    {
        return self::$routes;
    }

    public static function get($array)
    {
        if (count($array) === 2) {

            $uri = $array[0];
            // Je transforme la deuxième entrée en tableau dont le séparateur est "@"
            $callable = explode('@', $array[1]);
            array_push(
                self::$routes,
                [
                    'uri' => $uri, // la première entrée de $array
                    'controller' => $callable[0], // la première entrée de $callable
                    'method' => $callable[1] // la deuxième entrée de $callable
                ]
            );
            return new self;

        } else {
            trigger_error('L\'argument de la méthode Route::get() doit comporter deux entrées dans son array.');
        }
    }
    public static function name($str)
    {
        // Je vérifie si self::$routes n'est pas vide
        if (!empty(self::$routes)) {
            // Je récupère la dernière entrée du tableau self::$routes
            $last_entry = end(self::$routes);
            // Je définis un nouvel index "name" et sa valeur
            $last_entry['name'] = $str;
            // J'écrase la dernière entrée de self::$routes
            self::$routes[count(self::$routes)-1] = $last_entry;
            return new self;
        }
    }
}