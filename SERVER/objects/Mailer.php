<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    public static function sendMail(string $mailTo, string $subject, string $body, string $altBody,
                                   string $host = "eagle.addhost.pl", string $username = 'pqcms-noreply@poleq.pl', string $password = '9Uqxh69c', int $port = 465,
                                    array $files = []): array
    {
//Load Composer's autoloader
        require(dirname(__DIR__)."/vendor/autoload.php");

//Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host = $host;                     //Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   //Enable SMTP authentication
            $mail->Username = $username;                     //SMTP username
            $mail->Password = $password;                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port = $port;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->Encoding = PHPMailer::ENCODING_BASE64;  //this code very important
            $mail->SMTPDebug = false;
            $mail->addCustomHeader('Content-Language: pl-PL');
            $mail->SetLanguage("pl", dirname(__DIR__)."/vendor/phpmailer/phpmailer/language/");



            //Recipients
//            $mail->setFrom('pqcms-noreply@poleq.pl', 'PQCMS');
            $mail->setFrom($username, 'PQCMS');
            $mail->addAddress($mailTo);     //Add a recipient
//    $mail->addAddress('ellen@example.com');               //Name is optional
//    $mail->addReplyTo('pqcms-info@poleq.pl', 'Information');
//    $mail->addCC('cc@example.com');
//    $mail->addBCC('poleq@poleq.pl');

            //Attachments
            foreach($files as $file)
                $mail->addAttachment($file);         //Add attachments
//    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $altBody;


            return ["suc" => $mail->send()];
        } catch (Exception) {
            return ["suc" => 0, "err" => $mail->ErrorInfo];
        }
    }
}