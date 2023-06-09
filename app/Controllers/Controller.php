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

        $this->twig = new Environment($this->loader);
        $customExtension = new TwigExtensions();
        $this->twig->addExtension($customExtension);
    }
}