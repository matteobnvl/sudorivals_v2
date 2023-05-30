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
        require __BASEPATH__ . '/app/Route.php';
        require __BASEPATH__ . '/app/routes/web.php';
        require __BASEPATH__ . '/app/Kernel.php';
    }
}

// Liste des fichiers à importer dans le scope principal
getFiles();


if (!function_exists('super_printer')) {

    function super_printer($var)
    {
        ob_start(); ?>
        <style>
            .dump-container {
                display: flex;
                max-width: 90%;
                width: 400px;
                flex-direction: column;
                justify-content: flex-start;
                align-items: flex-start;
                background-color: #f1f1f1;
                border-radius: 6px;
                box-shadow: 6px 6px 12px rgba(0, 0, 0, 0.5);
                font-family: 'Arial', sans-serif;
            }
            .dump-container h4 {
                margin: 0 0 10px 0;
                font-size: 22px;
                padding: 5px 10px;
                border-top-left-radius: 6px;
                border-top-right-radius: 6px;
                background-color: #e1e1e1;
                width: calc(100% - 20px);
            }
            .dump-container p {
                margin: 0 0 10px 0;
                font-weight: bold;
                font-size: 14px;
                padding: 5px 10px;
            }
            .dump-container pre {
                padding: 5px 8px;
                background-color: #333;
                color: #fafafa;
                border-radius: 6px;
                margin: 10px 20px;
                overflow: scroll;
                max-height: 400px;
                width: calc(100% - 56px);
                box-shadow: 6px 6px 12px rgba(0, 0, 0, 0.7) inset;
            }
        </style>
        <div class="dump-container">
            <h4>Printer</h4>
            <p>Le <?= date("d-m-Y") ?> à <?= date("H:i:s") ?></p>
            <pre>
            <?php var_dump($var); ?>
        </pre>
        </div>
        <?php echo ob_get_clean();
    }
}

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