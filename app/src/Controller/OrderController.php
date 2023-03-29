<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\StatusRepository;
use App\Services\CustomMailerService;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/order")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("", name="order_index", methods={"GET"})
     */
    public function index(Request $request, OrderRepository $orderRepository, StatusRepository $statusRepository): Response
    {
        $query = $request->query->all();
        $status = key_exists('status', $query) ? $query['status'] : null;
        if (!in_array($status, ['1', '2', '3', '4'])){
            $status = null;
        }
        return $this->render('order/index.html.twig', [
            'orders' => $orderRepository->getOrdersListSortedByDate(
                $status,
                key_exists('start', $query) ? $query['start'] : null,
                key_exists('end', $query) ? $query['end'] : null
            ),
            'isset_status' => $status,
            'statuses' => $statusRepository->findAll()
        ]);
    }

//    /**
//     * @Route("/new", name="order_new", methods={"GET","POST"})
//     */
//    public function new(Request $request, Security $security, EntityManagerInterface $entityManager, CustomMailerService $service): Response
//    {
//        $order = new Order();
//        $form = $this->createForm(OrderType::class, $order);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->persist($order);
//            $entityManager->flush();
//            $numberOrder = $order->getId();
//            $mailDescription = "<h2>Заказ №$numberOrder создан.</h2>";
//            foreach ($order->getOrderProducts() as $orderProduct){
//                $quantity = $orderProduct->getQuantity();
//                $price = $orderProduct->getProduct()->getPrice();
//                $title = $orderProduct->getProduct()->getTitle();
//                $mailDescription.="<p>$title х$quantity $price р./шт.</p>";
//            }
//            $service->sendMail($order->getUser()->getEmail(), 'Dear customer', 'Утвержден комментарий от STROY-BEL',
//                "$mailDescription");
//
//            return $this->redirectToRoute('order_index');
//        }
//
//        return $this->render('order/new.html.twig', [
//            'order' => $order,
//            'form' => $form->createView(),
//        ]);
//    }

    /**
     * @Route("/{id}", name="order_show", methods={"GET"})
     */
    public function show(Order $order): Response
    {
        $user = $this->getUser();
        if (($user && $user != $order->getUser() && $user->getRoles()[0] != 'ROLE_ADMIN') || $user == null){
//            return $this->render('bundles/TwigBundle/Exception/error.html.twig');
            return $this->render('error.html.twig');
        }
        $orderedProducts = $order->getOrderProducts();
        return $this->render('order/show.html.twig', [
            'order' => $order,
            'orderedProducts' => $orderedProducts
        ]);
    }

    /**
     * @Route("/{id}/edit", name="order_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Order $order, CustomMailerService $service): Response
    {
        $user = $this->getUser();
        if (($user && $user->getRoles()[0] != 'ROLE_ADMIN') || $user == null){
//            return $this->render('bundles/TwigBundle/Exception/error.html.twig');
            return $this->render('error.html.twig');
        }
        $form = $this->createForm(OrderType::class, $order);

        $form->remove('address');
        $form->add(
            'status',
            null,
            ['label' => "Cтатус"]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->getStatus()->getName();
            $order->getid();
            $service->sendMail($order->getUser()->getEmail(), 'Dear customer', 'Изменение статуса заказа STROY-BEL',
                "");
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('order_index');
        }

        return $this->render('order/edit.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="order_delete", methods={"POST"})
     */
    public function delete(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (($user && $user->getRoles()[0] != 'ROLE_ADMIN') || $user == null){
//            return $this->render('bundles/TwigBundle/Exception/error.html.twig');
            return $this->render('error.html.twig');
        }
        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->request->get('_token'))) {
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('order_index');
    }
}
