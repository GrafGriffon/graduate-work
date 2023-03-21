<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;

class CustomMailerService
{
    private const HOST = 'ssl://smtp.mail.ru';
    private const MY_MAIL = 'illiaa552@mail.ru';
    private const PASSWORD = 'UjKkwDFLgmfVCvfSpssM';
    private const PROTOCOL = 'SSL';
    private const PORT = '465';
    private const MY_FULL_NAME = 'Illia Kurbatski';

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
        $mail->Host = self::HOST;
        $mail->SMTPAuth = true;
        $mail->Username = self::MY_MAIL;
        $mail->Password = self::PASSWORD; // пароль от почтового ящика
        $mail->SMTPSecure = self::PROTOCOL;
        $mail->Port = self::PORT;

        $mail->CharSet = 'UTF-8';
        $mail->From = self::MY_MAIL;  // адрес почты, с которой идет отправка
        $mail->FromName = self::MY_FULL_NAME; // имя отправителя
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