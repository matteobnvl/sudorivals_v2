<?php

namespace App\Controllers;

use App\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {

        //return $this->twig->render('home/index.html.twig', ['users' => $users]);
        return $this->twig->render('home/index.html.twig', []);
    }
}