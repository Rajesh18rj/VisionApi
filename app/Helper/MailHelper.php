<?php

namespace App\Helper;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Symfony\Component\Process\Process;

class MailHelper
{

    public static function SendMail($mail_to_address, $Subject, $mailContent, $bcc_address = [], $attachment = [])
    {
        try {
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION');
            $mail->Port = env('MAIL_PORT');
            $mail->setFrom(env('MAIL_USERNAME'), 'camlytix');
            // self::MailConfiguration($mail);
            $mail->addAddress($mail_to_address, $mail_to_address);
            if (count($bcc_address) > 0) {
                foreach ($bcc_address as $key => $value) {
                    $mail->AddBCC($value, $value);
                }
            }
            if (count($attachment) > 0) {
                foreach ($attachment as $key => $value) {
                    $mail->addAttachment($value["path"], $value["name"]);
                }
            }
            // $mail->AddAttachment(storage_path() . '//app//' . 'STM-Gift-Aid-Declaration.pdf', 'STM-Gift-Aid-Declaration.pdf');
            $mail->Subject = $Subject;
            $mail->isHTML(true);
            $mail->Body = $mailContent;
            if ($mail->send()) {
                return ["Type" => "S", "Message" => 'Message has been sent'];
            } else {
                return ["Type" => "E", "Message" => 'Mailer Error: ' . $mail->ErrorInfo];
            }
        } catch (Exception $e) {
            return ["Type" => "E", "Message" => 'Mailer Error: ' . $e->getMessage()];
        }
    }



    // public static function MailConfiguration($mail)
    // {
    //     $mail->isSMTP();
    //     $mail->Host = "smtp.office365.com";
    //     $mail->SMTPAuth = true;
    //     $mail->Username = "teamit@ampoweruk.com";
    //     $mail->Password = "Toq53971";
    //     $mail->SMTPSecure = 'tls';
    //     $mail->Port = 587;
    //     $mail->setFrom('teamit@ampoweruk.com', 'Ampower');
    // }

    //public static function MailConfiguration($mail)
    //{
    //  $mail->isSMTP();
    //$mail->Host = "smtp.gmail.com";
    //$mail->SMTPAuth = true;
    //$mail->Username = "alphaesolutionscbe@gmail.com";
    //$mail->Password = "ncssdaxevqnqohvk";
    //$mail->SMTPSecure = 'ssl';
    // $mail->Port = 465;
    //$mail->setFrom('alphaesolutionscbe@gmail.com', 'Alpha E Solutions');

    //     MAIL_DRIVER=smtp
    // MAIL_HOST="smtp.office365.com"
    // MAIL_PORT=587
    // MAIL_USERNAME="teamit@ampoweruk.com"
    // MAIL_PASSWORD="Toq53971"
    // MAIL_ENCRYPTION='tls'

    // IMAP_HOST=outlook.office365.com
    // IMAP_PORT=993
    // IMAP_ENCRYPTION=ssl
    // IMAP_VALIDATE_CERT=false
    // IMAP_USERNAME=customersupport@ampoweruk.com
    // IMAP_PASSWORD=Kap42113
    // IMAP_DEFAULT_ACCOUNT=default
    // IMAP_PROTOCOL=imap


    // MAIL_DRIVER=smtp
    // MAIL_HOST="bugis.sgcloudhosting.com"
    // MAIL_PORT=465
    // MAIL_USERNAME="noreply@srindusoftpl.com"
    // MAIL_PASSWORD="Ex10sion$#."
    // MAIL_ENCRYPTION='ssl'
    //}
}
