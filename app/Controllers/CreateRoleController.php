<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Role;

class CreateRoleController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['id'])) {
            redirect('Deconnexion');
        }
    }

    public function index()
    {
        $checker = $this->checkCreateRole($_POST);

        return view('auth.createRole', compact('checker'));
    }

    private function checkCreateRole($post){
        if(isset($post['check']) && $post['check'] == 'ok'){
            if(isset($post['nom_role']) && $post['nom_role'] != ''){
                Role::createRole($post);
                redirect('Tableau de bord');
            }else{
                return "Veuillez rentrer un nom avant de valider.";
            }
        }
    }
}