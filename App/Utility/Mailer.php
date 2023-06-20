<?php

namespace App\Utility;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class Mailer
{
    public function SendMail($subject, $body, $dest)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.ethereal.email';
            $mail->SMTPAuth = true;
            $mail->Username = 'lindsay.oreilly40@ethereal.email';
            $mail->Password = 'xmYeYPFpDsjHbVmXRc';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('contact@vgel.com', 'Mailer');
            $mail->addAddress($dest);

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            // return var_dump($mail->Host . $mail->Username . $mail->Port);
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"; // FOR DEV TODO: change this 
        }
    }
}
