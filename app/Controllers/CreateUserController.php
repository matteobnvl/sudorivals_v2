<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;

class CreateUserController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['id'])) {
            redirect('Deconnexion');
        }
    }

    public function index()
    {
        $checker = $this->checkCreateUser($_POST);

        return view('auth.createUser',compact('checker'));
    }

    private function checkCreateUser($post){
        if(isset($post['check']) && $post['check'] == 'ok'){
            if($this->verifFormulaireUser() == True){
                User::createUser($post);
                redirect('Tableau de bord');
            }else{
                return 'Tout les Champs ne sont pas remplie correctement';
            }
        }
    }

    private function verifFormulaireUser(){
        if(isset($_POST['firstname']) && $_POST['firstname'] !== '' &&
            isset($_POST['lastname']) && $_POST['lastname'] !== '' &&
            isset($_POST['email']) && $_POST['email'] !== '' &&
            isset($_POST['password']) && $_POST['password'] !== '' &&
            isset($_POST['password2']) && $_POST['password2'] !== '' &&
            isset($_POST['id_role']) && $_POST['id_role'] !== '')
            {
                if($_POST['password'] == $_POST['password2'] && !User::verifEmail($_POST['email'],$_POST['password'])){
                    return True;
                }else{
                    return False;
                }

            }else{
                return False;
            }
    }
}