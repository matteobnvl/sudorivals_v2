<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;

class DeleteController extends Controller
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
        $checkDelete = $this->VerifDelete($_GET['user'],$_POST);

        return view('auth.delete', compact('user'));
    }

    private function VerifDelete($id,$post){
        if(isset($post['supprimer']) && $post['supprimer'] == 'oui'){
            User::deleteUser($id);
            redirect('Tableau de bord');
        }elseif(isset($post['supprimer']) && $post['supprimer'] == 'non'){
            redirect('Tableau de bord');
        }
    }
}