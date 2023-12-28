<?php

namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\TwigExtensions;

class Controller
{
    private $loader;
    protected $twig;

    public function __construct()
    {
        $this->loader = new FilesystemLoader('./templates');

        
        $user = !empty($_SESSION) ? $_SESSION : null;
        $this->twig = new Environment($this->loader);
        $this->twig->addGlobal('user', $user);

        $customExtension = new TwigExtensions();
        $this->twig->addExtension($customExtension);
    }
}