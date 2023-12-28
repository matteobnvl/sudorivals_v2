<?php

namespace App\Models;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail extends Model
{
    protected $email;

    public function __construct()
    {
        $this->email = new PHPMailer();
        $this->email->isSMTP();
        $this->email->Host = env('HOST_MAIL');
        $this->email->SMTPAuth = true;
        $this->email->Username = env('HOST_MAIL');
        $this->email->Password = env('PASSWORD_MAIL');
        $this->email->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->email->Port = env('PORT_MAIL');
    }

    public function send($emailTo, $subject, $body)
    {
        try {
            $this->email->setFrom(env('EMAIL_SENT_FROM'));
            $this->email->addAddress($emailTo);
    
            $this->email->isHTML(true);
            $this->email->Subject = $subject;
            $this->email->Body = $body;
    
            $this->email->send();
            return true;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$this->email->ErrorInfo}";
        }
    }

    public function ForgotPasswordMail($post, $token) {                                //Set email format to HTML
        $subject = 'Mot de passe oublié';
        $body = 'Pour votre nouveau mot de passe veuillez cliquer sur l\'adresse ci-dessous : <br><br>
                        . ' . $_ENV['APP_URL'] . '/new-password?token=' . $token . ' <br><br>
                        Si ce message ne vous est pas destiné, veuillez l\'ignorer.' ;
        $from = $post['email'];

        return $this->send($from, $subject, $body);
    }

    public function sendMailDemandeAmis($amis)
    {
        $subject = 'Demande d\'amis !';
        $body = 'Hey '. $amis['pseudo'] .', Vous avez reçu une demande d\'amis de la part de '. $_SESSION['pseudo'].'.<br><br>
                        Pour l\'accepter cliquer ici sur ce lien : <br><br>' . $_ENV['APP_URL'] . '/ajouter-amis. <br><br>
                        Si ce message ne vous est pas destiné, veuillez l\'ignorer.' ;
        $from = $amis['email'];
        
        return $this->send($from, $subject, $body);
    }

    public function sendComment($name, $email, $comment)
    {
        $subject = 'Nouveau message';
        $body = 'Nouveau message depuis le site de la part de '. $name .'.<br><br>
                        Message : ' . $comment . ' <br><br>
                        Email : ' . $email . ' <br><br>';
        $from = env('HOST_MAIL');
        return $this->send($from, $subject, $body);
    }


    public function sendRemerciementContact($name, $email)
    {
        $subject = 'Suite à votre contact';
        $body = 'Bonjour '. $name .', toute l\'équipe de MySudoku vous remercie pour votre message.<br><br>
                        Nous le traiterons dans les plus brefs délais, pour vous faire le meilleur retour possible.
                        En attendant vous pouvez compléter une grille de sudoku pour gagner encore plus de points !<br><br>
                        Bonne journée.<br><br>Cordialement,<br><br>L\'équipe MySudoku.';
        $from = $email;
        return $this->send($from, $subject, $body);
    }
}