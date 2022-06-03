<?php

namespace App;


namespace App;

class Request
{
    private static array $current =[];
    private static array $links = [];

    public static function parseRoutes($routes)
    {
        self::$links = $routes;
        $uri = $_SERVER['REQUEST_URI'];
        $uri = explode('?', $uri)[0];
        $uri = explode('#', $uri)[0];
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" 
            : "http://";
        $fullUrl = $protocol . $_SERVER['HTTP_HOST'] . $uri;
        $uri = str_replace(env('APP_URL'), '', $fullUrl);
        $check = false;

        foreach($routes as $route){

            if($route['uri'] === $uri){
                $check = true;
                self::$current = $route;
                $controller = $route['controller'];
                $method = $route['method'];


                try{

                    echo call_user_func_array([new $controller, $method], []);


                }catch (\Error $e){
                    echo $e->getMessage();
                }
            }
        }
        if($check === false){
            abort(404);
        }
    }

    public static function currentRoute()
    {
        return self::$current;
    }

    public static function route($name)
    {
        foreach (self::$links as $link) {
            if ($link['name'] === $name) {
                return env('APP_URL') . $link['uri'];
            }
        }
        trigger_error('La route "' . $name . '" n\'existe pas...');
    }
}