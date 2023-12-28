<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;
use App\Models\Mail;

class AuthentificationController extends Controller
{
    private $user;
    private $mail;

    function __construct()
    {   
        parent::__construct();
        $this->user = new User();
        $this->mail = new Mail();
    }

    public function login()
    {
        if ($_SESSION) {
            redirect('app_dashboard');
        }
        $forgotpassword = false;
        $mail = '';
        $checkMail = '';
        $message = '';
        if($_GET){
            if(isset($_GET['forgotpassword'])){
                $forgotpassword = true;
                if(isset($_GET['mail']) && $_POST) {
                    if($_POST['mail'] !== ''){
                        if(!$this->user->checkMail($_POST)){
                            $mail = true;
                        } else {
                            if ($this->user->verifAskResetPassword($_POST['mail'])) {
                                $mail = true;
                                $string = implode('', array_merge(range('A','Z'), range('a','z'), range('0','9')));
                                $token = substr(str_shuffle($string), 0, 30);
                                $this->user->InsertTokenDate($_POST['mail'], $token);
                                $checkMail = $this->mail->ForgotPasswordMail($_POST, $token);
                            } else {
                                $mail = false;
                                $message = 'Une demande a déjà été formulée.<br> Consultez votre boite mail.';
                            }
                        }
                    }
                }
            }
        }
        $checker = $this->checkIfConnectionSent();
        $pages = 'login';
        return $this->twig->render('authentication/login.html.twig',[
            'pages' => $pages,
            'error' => $checker,
            'forgotpassword' => $forgotpassword,
            'mail' => $mail,
            'checkMail' => $checkMail,
            'message' => $message
        ]);
    }

    public function logout()
    {
        setcookie('remember_token', '', time() - 3600, '/');
        $this->user->deleteCookie();
        session_destroy();
        redirect('Accueil');
    }

    public function register()
    {
        if ($_SESSION) {
            redirect('app_dashboard');
        }
        $error = '';
        if ($_POST) {
            if ($_POST['email'] && $_POST['email'] ==! '' ||
                $_POST['pseudo'] && $_POST['pseudo'] ==! '' ||
                $_POST['password'] && $_POST['password'] ==! '') {
                    if ($this->user->checkMailAndPseudo($_POST['email'], $_POST['pseudo'])) {
                        if ($this->user->register($_POST)) {
                            $this->user->login($_POST['email'], $_POST['password']);
                            redirect('app_dashboard');
                        }

                    } else {
                        $error = "L'email ou le pseudo existe déjà";
                    }
                }
        }

        return $this->twig->render('authentication/register.html.twig', [
            'error' => $error
        ]);
    }

    public function ResetPassword() {
        if ($_GET && isset($_GET['token'])) {

            if ($_POST) {
                if (
                    $_POST['password'] !== '' &&
                    $_POST['password_reset'] !== '' &&
                    $_POST['password'] === $_POST['password_reset']
                ) {
                    $user = $this->user->UserByToken($_GET['token']);
                    $this->user->ResetPassword($_POST, $_GET['token']);
                    $this->user->login($user[0]['email'], $_POST['password']);
                    redirect('app_dashboard');
                }
            }


            if ($this->user->VerifToken($_GET['token'])) {
                return $this->twig->render('authentication/new_password.html.twig', [
                    'pages' => 'dashboard'
                ]);
            }
        }
        redirect('Accueil');
    }

    private function checkIfConnectionSent() {

        if ($_POST) {
            if (isset($_POST['email']) && $_POST['email'] !== '') {
                if (isset($_POST['password']) && $_POST['password'] !== '') {
                    if (!$this->user->login($_POST['email'], $_POST['password'])) {
                        return 'Les identifiants ne correspondent pas...';
                    } else {
                        if (!isset($_COOKIE['remember_token'])) {
                            $string = implode('', array_merge(range('A','Z'), range('a','z'), range('0','9')));
                            $token = substr(str_shuffle($string), 0, 50);;
                            $this->user->insertToken($token);
                            $timestamp = time() + (30 * 24 * 60 * 60);
                            setcookie('remember_token', $token, $timestamp);;
                        }
                        redirect('app_dashboard');
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