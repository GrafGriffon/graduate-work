<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Newsletter;
use App\Form\CategoryType;
use App\Form\NewsletterType;
use App\Repository\CategoryRepository;
use App\Repository\NewsletterRepository;
use App\Services\CustomMailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/newsletter")
 */
class NewsletterController extends AbstractController
{
    /**
     * @Route("/", name="newsletter_index", methods={"GET"})
     */
    public function index(NewsletterRepository $repository): Response
    {
        return $this->render('newsletter/index.html.twig', [
            'newsletters' => $repository->findBy(['isAccepted' => true]),
        ]);
    }

    /**
     * @Route("/new", name="newsletter_new", methods={"GET","POST"})
     */
    public function new(Request $request, NewsletterRepository $repository, EntityManagerInterface $entityManager, CustomMailerService $service): Response
    {
        $form = $this->createForm(NewsletterType::class);
        $mailArray = $request->request->get('newsletter');
        if ($mailArray && filter_var($mailArray['mail'], FILTER_VALIDATE_EMAIL) && !$repository->findOneBy(['mail' => $mailArray['mail']])) {
$newsletter = new Newsletter($mailArray['mail']);
            $entityManager->persist($newsletter);
            $entityManager->flush();
            $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $hash=$newsletter->getHash();
            $urlAccept = ($url . '/accept/' . $hash);
            $urlCancel = ($url . '/cancel/' . $hash);
            $service->sendMail($mailArray['mail'], 'Dear customer', 'Подпись на рассылку от STROY-BEL',
                "<h1>Оповоещение о рассылке</h1><p>Уважаемый клиент, желаете ли подписаться на нашу рассылку: </p><a href=\"$urlAccept\">Да, хочу получать рассылку</a><br><a href=\"$urlCancel\">Нет, не хочу получать рассылку</a>");

            return $this->redirectToRoute('newsletter_index');
        }
        return $this->render('newsletter/new.html.twig', [
            'category' => null,
            'form' => $form->createView(),
        ]);
    }
}
