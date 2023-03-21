<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Repository\CategoryRepository;
use App\Repository\NewsletterRepository;
use App\Repository\NewsRepository;
use App\Repository\ProductRepository;
use App\Services\CustomMailerService;
use Doctrine\DBAL\SQL\Parser\Exception;
use Doctrine\ORM\EntityManagerInterface;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mail")
 */
class MailController extends AbstractController
{
    /**
     * @Route("", name="mail", methods={"POST"})
     * @param ProductRepository $productRepository
     * @param NewsRepository $newsRepository
     * @param CategoryRepository $categoryRepository
     * @return Response
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function index(Request $request, CustomMailerService $service, EntityManagerInterface $manager, NewsletterRepository $repository): Response
    {
        $mail = $request->request->get('EMAIL');
        if (!$repository->findOneBy(['mail'=>$mail])){
            $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $newsletter = new Newsletter($mail);
            $manager->persist($newsletter);
            $manager->flush();
            $hash=$newsletter->getHash();
            $urlAccept = ($url . '/accept/' . $hash);
            $urlCancel = ($url . '/cancel/' . $hash);
            $service->sendMail($mail, 'Dear customer', 'Подпись на рассылку от STROY-BEL',
                "<h1>Оповоещение о рассылке</h1><p>Уважаемый клиент, желаете ли подписаться на нашу рассылку: </p><a href=\"$urlAccept\">Да, хочу получать рассылку</a><br><a href=\"$urlCancel\">Нет, не хочу получать рассылку</a>");
        }
        return $this->redirect('/');
    }

    /**
     * @Route("/accept/{hash}", name="mail-accept", methods={"GET"})
     */
    public function accept(string $hash, NewsletterRepository $repository, EntityManagerInterface $manager): Response
    {
        if ($newsletter = $repository->findOneBy(['hash' => $hash])){
            $newsletter->setIsAccepted(true);
            $manager->flush();
        }
        return $this->redirect('/');
    }

    /**
     * @Route("/cancel/{hash}", name="mail-cancel", methods={"GET"})
     */
    public function cancel(string $hash, NewsletterRepository $repository, EntityManagerInterface $manager): Response
    {
        if ($newsletter = $repository->findOneBy(['hash' => $hash])){
            $manager->remove($newsletter);
            $manager->flush();
        }
        return $this->redirect('/');
    }
}
