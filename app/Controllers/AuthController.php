<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function login()
    {
        $checker = $this->checkIfConnectionSent();
        return view('auth.login', compact('checker'));
    }

    public function logout()
    {
        session_destroy();
        redirect('Accueil');
    }

    private function checkIfConnectionSent() {

        if (isset($_POST['check']) && $_POST['check'] === 'ok') {
            if (isset($_POST['email']) && $_POST['email'] !== '') {
                if (isset($_POST['password']) && $_POST['password'] !== '') {
                    if (!User::checkUser($_POST['email'], $_POST['password'])) {
                        return 'Les identifiants ne correspondent pas...';
                    } else {
                        redirect('Tableau de bord');
                    }
                } else {
                    return "Le mot de passe doit être renseigné";
                }
            } else {
                return "L'email doit être renseigné";
            }
        }
    }
}