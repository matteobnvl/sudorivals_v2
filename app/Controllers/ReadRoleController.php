<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Role;

class ReadRoleController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['id'])) {
            redirect('Deconnexion');
        }
    }

    public function index()
    {
        $roles = Role::selectAll();

        return view('auth.readRole', compact('roles'));
    }
}