<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Services\CustomMailerService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment", name="comment")
     */
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findBy([],['date' => 'DESC']),
        ]);
    }

    /**
     * @Route("/comment/accept/{id}", name="comment_accept")
     */
    public function accept(EntityManagerInterface $manager, Comment $comment, CustomMailerService $service): Response
    {
        $title = $comment->getProduct()->getTitle();
        $service->sendMail($comment->getUser()->getEmail(), 'Dear customer', 'Утвержден комментарий от STROY-BEL',
            "<p>Уважаемый клиент, ваш комментарий для товара $title утвержден</p>");

        $comment->setIsAccepted(true);
        $manager->flush();
        return $this->redirectToRoute('comment');
    }

    /**
     * @Route("/comment/delete/{id}", name="comment_delete")
     */
    public function delete(EntityManagerInterface $manager, Comment $comment): Response
    {
        $manager->remove($comment);
        $manager->flush();
        return $this->redirectToRoute('comment');
    }

    /**
     * @Route("/comment/new", name="new_comment")
     * @param Request $request
     * @param ProductRepository $productRepository
     * @param Security $security
     * @param UserRepository $userRepository
     * @return Response
     */
    public function new(Request $request, ProductRepository $productRepository, Security $security, UserRepository $userRepository): Response
    {
        $comment = new Comment();
        $entityManager = $this->getDoctrine()->getManager();
        $product = $productRepository->findOneBy(['id' => $_GET['productId']]);

        $comment->setUser($userRepository->findOneBy(['email' => $security->getUser()->getUsername()]));
        $comment->setProduct($product);
        $comment->setDate(new DateTime());
        $comment->setBody($request->query->get('body'));
        $comment->setRating((int)$request->query->get('rating'));
        $entityManager->persist($comment);
        $entityManager->flush();
        return $this->redirectToRoute('view', ['id' => $_GET['productId']]);
    }
}
