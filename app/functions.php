<?php

use App\Request;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

if (!function_exists('setErrorsDisplay')) {
    function setErrorsDisplay($mode)
    {
        if ($mode === true) {
        	ini_set('display_errors', 1);
       	 	ini_set('display_startup_errors', 1);
        	error_reporting(E_ALL);
        }
    }
}


if (!function_exists('env')) {

    function env($key)
    {
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        return null;
    }
}



if (!function_exists('getFilesRecursively')) {
    function getFilesRecursively($path, $except)
    {
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                if (is_file($path .'/' . $file) && $file !== $except) {
                    require $path .'/' . $file;
                } else if (is_dir($path .'/' . $file)) {
                    getFilesRecursively($path .'/' . $file, $except);
                }
            }
        }
    }
}


if (!function_exists('getFiles')) {

    function getFiles()
    {
    	// Je récupère tous les fichiers dans tools/traits
        getFilesRecursively(__BASEPATH__ . '/app/Traits', '');
        // Je récupère tous les fichiers dans tools/interfaces
        getFilesRecursively(__BASEPATH__ . '/app/Interfaces', '');
        require __BASEPATH__ . '/app/Models/Model.php';
        require __BASEPATH__ . '/app/Controllers/Controller.php';
        // Je récupère tous les fichiers dans app/models sauf Model.php
        getFilesRecursively(__BASEPATH__ . '/app/Models', 'Model.php');
        // Je récupère tous les fichiers dans app/controllers sauf Controller.php
        getFilesRecursively(__BASEPATH__ . '/app/Controllers', 'Controller.php');
        require __BASEPATH__ . '/app/Request.php';
        require __BASEPATH__ . '/app/TwigExtensions.php';
        require __BASEPATH__ . '/app/Route.php';
        require __BASEPATH__ . '/app/routes/web.php';
        require __BASEPATH__ . '/app/Kernel.php';
    }
}

// Liste des fichiers à importer dans le scope principal
getFiles();

if (!function_exists('view')) {

    function view($page, $vars = null)
    {
        // Je remplace les séparateurs "." par "/"
        $page = str_replace('.', '/', $page);
        $path = __BASEPATH__ . '/templates/errors/';

        if (file_exists($path . $page  . '.html.twig')) {

            if ($vars !== null) {
                // J'extrais les variables de $vars si elles existent
                extract($vars);
            }

            // J'invoque template.php dans un objet Buffer puis le retourne
            ob_start();
            require $path . 'template.php';
            return ob_get_clean();

        } else {

            trigger_error('La vue "' . $page . '" n\'existe pas...');
        }
    }
}


if (!function_exists('abort')) {

    function abort($code)
    {
        $references = [
            '404' => 'Page non trouvée',
            '500' => 'Erreur serveur',
            '419' => 'Jeton de sécurité expiré',
            '301' => 'Redirection'
        ];
        $message = (isset($references[$code])) ? $references[$code] : '';

        $loader = new FilesystemLoader('./templates');

        $twig = new Environment($loader);

        http_response_code($code);
        echo $twig->render('errors/default.html.twig', compact('code', 'message'));
    }
}

if (!function_exists('getCurrentRoute')) {

    function getCurrentRoute()
    {
        return Request::currentRoute();
    }
}

if (!function_exists('route')) {

    function route(string $name, array $param = null) :string
    {
        return Request::route($name, $param);
    }
}

if (!function_exists('redirect')) {

    function redirect($name, $param = '')
    {
        header('Location: ' . route($name) . $param);
        exit();
    }
}


if (!function_exists('isConnected')) {

    function isConnected()
    {
        return isset($_SESSION['id']);
    }
}

if (!function_exists('dateDiff')) {
    function datediff($date1, $date2)
    {
        $date2 = strtotime($date2);
        $date1 = strtotime($date1);
        return abs($date1 - $date2);
    }
}

function intervalleDate($date = null) {
    // Si aucune date n'est fournie, utilisez la date d'aujourd'hui
    if ($date === null) {
        $date = new DateTime();
    }
    
    $dateActuelle = new DateTime();
    $date = new DateTime($date);
    $interval = $date->diff($dateActuelle);
    
    if ($interval->y > 0) {
        return $interval->format('%y ans, %m mois, %d jours');
    } elseif ($interval->m > 0) {
        return $interval->format('%m mois, %d jours');
    }
    return $interval->format('%d jours');
}