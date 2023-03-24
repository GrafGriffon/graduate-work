<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use App\Services\CustomMailerService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CheckoutController extends AbstractController
{
    /**
     * @Route("/checkout", name="checkout")
     */
    public function index(Request $request, Security $security, ProductRepository $productRepository, UserRepository $userRepository, StatusRepository $statusRepository, EntityManagerInterface $entityManager, CustomMailerService $service)
    {
        $order = new Order();
        $user = $security->getUser();
        $form = $this->createForm(OrderType::class, $order, ['user' => $userRepository->findOneBy(['email' => $user->getUsername()])->getId()]);
        $form->handleRequest($request);
        $productListInCart = [];
        $coast = 0;
        if ($form->isSubmitted() && $form->isValid()) {
            $order->setUser($user);
            $order->setStatus($statusRepository->findOneBy(['sort' => 1]));
            $order->setDate(new DateTime());

            $entityManager->persist($order);
            $entityManager->flush();

            if (!isset($_COOKIE['cart'])) {
                setcookie('cart', "", time() + 86400, "/");
            } else {
                $cookie = json_decode($_COOKIE['cart']);
            }

            if(!isset($cookie)){
                return $this->redirectToRoute('main');
            }
            $orderedProducts = array_count_values($cookie);
            $numberOrder = $order->getId();
            $mailDescription = "<h2>Заказ №$numberOrder создан.</h2>";
            foreach ($orderedProducts as $productId => $quantity) {
                if ($productId != 0) {
                    $orderedProduct = new OrderProduct();
                    $orderedProduct->setProduct($productRepository->findOneBy(['id' => $productId]));
                    $orderedProduct->setOrder($order);
                    $orderedProduct->setQuantity($quantity);
                    $entityManager->persist($orderedProduct);
                    $entityManager->flush();
                    $quantity = $orderedProduct->getQuantity();
                    $price = $orderedProduct->getProduct()->getPrice();
                    $title = $orderedProduct->getProduct()->getTitle();
                    $mailDescription.="<p>$title х$quantity $price р./шт.</p>";
                }
            }
            $service->sendMail($order->getUser()->getEmail(), 'Dear customer', 'Утвержден комментарий от STROY-BEL',
                "$mailDescription");
            setcookie('cart', "", time() + 86400, "/");
            return $this->redirectToRoute('checkout_success');
        }

        $productListInCart = [];

        if (isset($_COOKIE['cart'])) {
            $productListInCart = json_decode($_COOKIE['cart']);

        }

        foreach ($productListInCart as $product) {
            if ($product !== 0) {
                $coast += $productRepository->findOneBy(['id' => $product])->getPrice();
            }
        }
        return $this->render('checkout/index.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
            'coast' => $coast
        ]);
    }

    /**
     * @Route("/success", name="checkout_success")
     */
    public function showSuccess(): Response
    {
        return $this->render('checkout/success.html.twig', [
        ]);
    }
}
