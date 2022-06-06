<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;
use App\Models\Role;

class UpdateController extends Controller
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
        $checker = $this->checkUpdateUser();
        $role = Role::selectAll();

        return view('auth.update',compact('user','checker','role'));
    }

    private function checkUpdateUser(){
        
        if(isset($_POST['check']) && $_POST['check'] == 'ok'){
            if(isset($_POST['firstname']) && $_POST['firstname'] !== '' &&
            isset($_POST['lastname']) && $_POST['lastname'] !== '' &&
            isset($_POST['email']) && $_POST['email'] !== ''){
                if(User::verifEmail($_POST['email'],$_POST['password']) === True){
                    return "L'email rentré est déjà enregistré.";
                }else{
                    User::updateUserAdmin($_POST);
                    redirect('Tableau de bord');
                }
            }
        }
    }
}