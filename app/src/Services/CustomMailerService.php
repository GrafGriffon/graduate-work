<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;

class CustomMailerService
{
    private const HOST = 'ssl://smtp.mail.ru';

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendMail(
        string $to,
        string $recipientName,
        string $title,
        string $description,
        ?string $path = null
    ): void
    {
        $mail = new PHPMailer;
        $mail->isSMTP();

        $mail->SMTPDebug = 0;
        $mail->Host = $_ENV["MAIL_HOST"];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV["MAIL_POST"];
        $mail->Password = $_ENV["MAIL_PASSWORD"]; // пароль от почтового ящика
        $mail->SMTPSecure = $_ENV["MAIL_PROTOCOL"];
        $mail->Port = $_ENV["MAIL_PORT"];

        $mail->CharSet = 'UTF-8';
        $mail->From = $_ENV["MAIL_POST"];  // адрес почты, с которой идет отправка
        $mail->FromName = $_ENV["MAIL_FULL_NAME"]; // имя отправителя
        $mail->addAddress($to, $recipientName);
        $mail->isHTML();

        $mail->Subject = $title;
        $mail->Body = $description;
        if ($path){
            $mail->addAttachment($path);
        }
        $mail->send();
    }
}