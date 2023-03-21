<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Feedback;
use App\Entity\Newsletter;
use App\Form\CategoryType;
use App\Form\NewsletterType;
use App\Repository\CategoryRepository;
use App\Repository\FeedbackRepository;
use App\Repository\NewsletterRepository;
use App\Services\CustomMailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/feedback")
 */
class FeedbackController extends AbstractController
{
    /**
     * @Route("", name="feedback", methods={"POST"})
     */
    public function sendMail(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = $request->request->all();
        $entityManager->persist(new Feedback($data['name'], $data['mail'], $data['message']));
        $entityManager->flush();
        return $this->redirectToRoute('contacts');
    }
    /**
     * @Route("/", name="feedback_list", methods={"GET"})
     */
    public function index(FeedbackRepository $repository): Response
    {
        return $this->render('feedback/index.html.twig', [
            'feedbacks' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="feedback-delete", methods={"GET"})
     */
    public function cancel(int $id, FeedbackRepository $repository, EntityManagerInterface $manager): Response
    {
        if ($feedback = $repository->find($id)){
            $manager->remove($feedback);
            $manager->flush();
        }
        return $this->redirectToRoute('feedback_list');
    }
}
