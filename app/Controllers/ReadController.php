<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;
use App\Models\Role;

class ReadController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['id'])) {
            redirect('Deconnexion');
        }
        if (!isset($_GET['user'])) {
            redirect('Tableau de bord');
        }
    }

    public function index()
    {
        $user = User::selectUser($_GET['user']);

        return view('auth.read', compact('user'));
    }
}