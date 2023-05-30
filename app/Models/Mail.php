<?php

namespace App\Models;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
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
}