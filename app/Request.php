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

        foreach ($routes as $route) {
            // Vérifier si l'URI de la route correspond à la requête actuelle
            if (self::matchesRoute($route['uri'], $uri)) {
                $check = true;
                self::$current = $route;
                $controller = $route['controller'];
                $method = $route['method'];
                $params = self::extractParams($route['uri'], $uri);
                
                try {
                    // Appeler la méthode du contrôleur en passant les paramètres extraits
                    echo call_user_func_array([new $controller, $method], $params);
                } catch (\Error $e) {
                    echo $e->getMessage();
                }
            }
        }
        if($check === false){
            abort(404);
        }
    }

    private static function matchesRoute($route, $uri)
    {
        // Remplacer les paramètres dynamiques dans l'URI de la route par des regex
        $route = preg_replace('#\{[^/]+\}#', '[^/]+', $route);
        $route = str_replace('/', '\/', $route);

        // Vérifier si l'URI correspond à la route
        return preg_match('#^' . $route . '$#', $uri);
    }

    private static function extractParams($route, $uri)
    {
        $params = [];

        // Extraire les valeurs des paramètres de l'URI
        $routeParts = explode('/', $route);
        $uriParts = explode('/', $uri);

        foreach ($routeParts as $index => $part) {
            if (preg_match('/^\{([^\/]+)\}$/', $part, $matches)) {
                $paramName = $matches[1];
                $params[] = $uriParts[$index];
            }
        }

        return $params;
    }

    public static function currentRoute()
    {
        return self::$current;
    }

    public static function route(string $name, array $param = null)
    {
        foreach (self::$links as $link) {
            if ($link['name'] === $name) {
                $uri = $link['uri'];
                if ($param != null) {
                    foreach ($param as $key => $value) {
                        $uri = str_replace('{'.$key.'}', $value, $uri);
                    }
                }

                return env('APP_URL') . $uri;
            }
        }
        trigger_error('La route "' . $name . '" n\'existe pas...');
    }
}