<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;
use App\Models\Role;

class HomeController extends Controller
{
    public function index()
    {
        $users = User::selectAll();
        $roles = Role::selectAll();

        //return view('pages.home', ['users' => $users]);
        return view('pages.home', compact('users'));
    }
}