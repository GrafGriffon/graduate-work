<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\CategoryRepository;
use App\Repository\NewsRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     * @param ProductRepository $productRepository
     * @param NewsRepository $newsRepository
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function index(ProductRepository $productRepository, NewsRepository $newsRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('main/main.index.html.twig', [
            'controller_name' => 'MainController',
            'products' => $productRepository->getAFewProductsSortedByDate(),
            'newsList' => $newsRepository->getAFewElementsFromNewsListSortedByDate(),
            'categories' => $categoryRepository->findCategoriesSortedByTitle(),
        ]);
    }

    /**
     * @Route("/registrated", name="registrated")
     * @return Response
     */
    public function viewNotification(): Response
    {
        return $this->render('main/registered.html.twig');
    }

    /**
     * @Route("/activated", name="activated")
     * @return Response
     */
    public function viewActivated(): Response
    {
        return $this->render('main/activated.html.twig');
    }

    /**
     * @Route("/activation/{hash}", name="user-activation", methods={"GET"})
     */
    public function cancel(string $hash, UserRepository $repository, EntityManagerInterface $manager): Response
    {
        if ($user = $repository->findOneBy(['hash' => $hash])) {
            $user->setIsActivated(true);
            $manager->flush();
        }
        return $this->redirect('/activated');
    }

    /**
     * @Route("/download", name="download", methods={"GET"})
     */
    public function download(ProductRepository $repository)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $counter = 2;
        $activeWorksheet->setCellValueByColumnAndRow(1, 1, 'Название');
        $activeWorksheet->setCellValueByColumnAndRow(2, 1, 'Описание');
        $activeWorksheet->setCellValueByColumnAndRow(3, 1, 'Категория');
        $activeWorksheet->setCellValueByColumnAndRow(4, 1, 'Цена');
        $activeWorksheet->getColumnDimensionByColumn(1)->setWidth(20);
        $activeWorksheet->getColumnDimensionByColumn(2)->setWidth(50);
        $activeWorksheet->getColumnDimensionByColumn(3)->setWidth(20);

        foreach ($repository->findBy(['isActive' => true]) as $product) {
            $activeWorksheet->setCellValueByColumnAndRow(1, $counter, $product->getTitle());
            $activeWorksheet->setCellValueByColumnAndRow(2, $counter, $product->getDescription());
            $activeWorksheet->setCellValueByColumnAndRow(3, $counter, $product->getCategory()->getTitle());
            $activeWorksheet->setCellValueByColumnAndRow(4, $counter, $product->getPrice());
            $counter++;
        }
        $spreadsheet->getActiveSheet()->setAutoFilter('A1:D' . $counter);

        $writer = new Xlsx($spreadsheet);
        $writer->save('catalog.xlsx');
        $fileName = 'catalog';
        header('Content-type: application/octet-stream');
        header('Content-Length: ' . filesize($fileName . '.xlsx'));
        header('Content-Disposition: attachment; filename="Catalog.xlsx"');
        readfile($fileName . '.xlsx');
        unlink($fileName . '.xlsx');
        $this->redirect('/');
    }

    /**
     * @Route("/filter", name="order_filter", methods={"POST"})
     */
    public function filter(Request $request, EntityManagerInterface $entityManager, OrderRepository $orderRepository): Response
    {
        $status = null;
        if (isset($_POST['status'])) {
            switch ($_POST['status']) {
                case 'Создан':
                    $status = 1;
                    break;
                case 'В обработке':
                    $status = 2;
                    break;
                case 'Подтвержден':
                    $status = 3;
                    break;
                case 'Выполнен':
                    $status = 4;
                    break;
                default:
                    $status = 0;
                    break;
            }
        }

        if (isset($_POST['filter'])) {
            return $this->redirect('/order?start=' . $_POST['start'] . '&end=' . $_POST['end'] . '&status=' . $status);
        }
        $orders = $orderRepository->getOrdersListSortedByDate(
            $status != 0 ? $status : null,
            $_POST['start'] ?? null,
            $_POST['end'] ?? null
        );
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $counter = 2;
        $activeWorksheet->setCellValueByColumnAndRow(1, 1, 'Номер');
        $activeWorksheet->setCellValueByColumnAndRow(2, 1, 'Адрес');
        $activeWorksheet->setCellValueByColumnAndRow(3, 1, 'Email');
        $activeWorksheet->setCellValueByColumnAndRow(4, 1, 'Статус');
        $activeWorksheet->setCellValueByColumnAndRow(5, 1, 'Дата');
        $activeWorksheet->setCellValueByColumnAndRow(6, 1, 'Товары');
        $activeWorksheet->setCellValueByColumnAndRow(7, 1, 'Сумма');
//        $activeWorksheet->getColumnDimensionByColumn(1)->setWidth(20);
//        $activeWorksheet->getColumnDimensionByColumn(2)->setWidth(50);
//        $activeWorksheet->getColumnDimensionByColumn(3)->setWidth(20);
        /** @var Order $order */
        foreach ($orders as $order) {
            $products = '';
            $price = 0;
            foreach ($order->getOrderProducts() as $orderProduct) {
                $title = $orderProduct->getProduct()->getTitle();
                $onePrice = $orderProduct->getProduct()->getPrice();
                $quantity = $orderProduct->getQuantity();
                $price += $quantity * $onePrice;
                $products .= "$title $quantity шт $onePrice р/шт.";
            }
            $activeWorksheet->setCellValueByColumnAndRow(1, $counter, $order->getId());
            $activeWorksheet->setCellValueByColumnAndRow(2, $counter, $order->getAddress()->getCity() . ' ' . $order->getAddress()->getAddress());
            $activeWorksheet->setCellValueByColumnAndRow(3, $counter, $order->getUser()->getEmail());
            $activeWorksheet->setCellValueByColumnAndRow(4, $counter, $order->getStatus()->getName());
            $activeWorksheet->setCellValueByColumnAndRow(5, $counter, $order->getDate()->format('d-m-y'));
            $activeWorksheet->setCellValueByColumnAndRow(6, $counter, $products);
            $activeWorksheet->setCellValueByColumnAndRow(7, $counter, $price);
            $counter++;
        }
        $spreadsheet->getActiveSheet()->setAutoFilter('A1:G' . $counter);

        $writer = new Xlsx($spreadsheet);
        $writer->save('orders.xlsx');
        $fileName = 'orders';
        header('Content-type: application/octet-stream');
        header('Content-Length: ' . filesize($fileName . '.xlsx'));
        header('Content-Disposition: attachment; filename="Orders.xlsx"');
        readfile($fileName . '.xlsx');
        unlink($fileName . '.xlsx');

        return $this->redirect('/order?start=' . $_POST['start'] . '&end=' . $_POST['end'] . '&status=' . $status);
    }
}
