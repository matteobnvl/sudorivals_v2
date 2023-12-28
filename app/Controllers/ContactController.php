<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Mail;
use App\Models\User;

class ContactController extends Controller
{
    private $mail;
    private $user;

    public function __construct()
    {
        parent::__construct();
        $this->mail = new Mail();
        $this->user = new User();
    }
    public function index()
    {
        if ($_POST) {

            $secret = env('PRIVATE_KEY_CAPTCHA');
            $response = htmlspecialchars($_POST['g-recaptcha-response']);
            $remoteip = $_SERVER['REMOTE_ADDR'];
            $request = "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$remoteip";
            $get = file_get_contents($request);
            $decode = json_decode($get, true);

            if ($decode['success']) {
                $name = $_POST['name'];
                $email = $_POST['email'];
                $comment = $_POST['comment'];
                $this->mail->sendComment($name, $email, $comment);
                $this->mail->sendRemerciementContact($name, $email);
            }
        }
        redirect('Accueil', [], '#contact');
    }

    public function contacts()
    {
        return $this->twig->render('contact/index.html.twig', []);
    }
}