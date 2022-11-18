<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;
use App\Models\Role;

class HomeController extends Controller
{
    public function index()
    {

        //return view('pages.home', ['users' => $users]);
        return view('pages.home');
    }
}