<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\NewsRepository;
use App\Repository\ProductRepository;
use Doctrine\DBAL\SQL\Parser\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{
    /**
     * @Route("/mail", name="mail", methods={"POST"})
     * @param ProductRepository $productRepository
     * @param NewsRepository $newsRepository
     * @param CategoryRepository $categoryRepository
     * @return Response
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function index(ProductRepository $productRepository, NewsRepository $newsRepository, CategoryRepository $categoryRepository): Response
    {
            $mail = new PHPMailer;
            $mail->isSMTP();

            $mail->SMTPDebug = 1;

            $mail->Host = 'ssl://smtp.mail.ru';

            $mail->SMTPAuth = true;
            $mail->Username = 'illiaa552@mail.ru'; // логин от вашей почты
            $mail->Password = 'UjKkwDFLgmfVCvfSpssM'; // пароль от почтового ящика
            $mail->SMTPSecure = 'SSL';
            $mail->Port = '465';

            $mail->CharSet = 'UTF-8';
            $mail->From = 'illiaa552@mail.ru';  // адрес почты, с которой идет отправка
            $mail->FromName = 'Illia'; // имя отправителя
            $mail->addAddress('illiaa553@mail.ru', 'Illia');

            $mail->isHTML(true);

            $mail->Subject = 'Отправка письма с мыла Тайтл';
            $mail->Body = "Имя: 1";
//        $mail->addAttachment('path');

//проверка на отправку. Можно сделать вывод текста, можно отправить юзера на какую-то страницу
            $mail->SMTPDebug  = 0;
            $mail->send();
        return $this->redirect('/');
    }
}
