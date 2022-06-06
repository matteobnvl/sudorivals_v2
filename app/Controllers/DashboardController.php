<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;
use App\Models\Role;

class DashboardController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['id'])) {
            redirect('Deconnexion');
        }
    }

    public function index()
    {
        
        $users = User::selectAll();
        $roles = Role::selectAll();
        $checkDelete = null;

        if($_SESSION['id_role'] == 1){
            return view('auth.dashboardAdmin', compact('users','checkDelete'));
        }else{
            return view('auth.dashboardUser');
        }
    }
}