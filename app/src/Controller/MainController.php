<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\NewsRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        if ($user = $repository->findOneBy(['hash' => $hash])){
            $user->setIsActivated(true);
            $manager->flush();
        }
        return $this->redirect('/registrated');
    }

    /**
     * @Route("/download", name="download", methods={"GET"})
     */
    public function download(ProductRepository $repository)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $counter = 2;
        $activeWorksheet->setCellValueByColumnAndRow(1,1, 'Название');
        $activeWorksheet->setCellValueByColumnAndRow(2,1, 'Описание');
        $activeWorksheet->setCellValueByColumnAndRow(3,1, 'Категория');
        $activeWorksheet->setCellValueByColumnAndRow(4,1, 'Цена');
        $activeWorksheet->getColumnDimensionByColumn(1)->setWidth(20);
        $activeWorksheet->getColumnDimensionByColumn(2)->setWidth(50);
        $activeWorksheet->getColumnDimensionByColumn(3)->setWidth(20);

        foreach ($repository->findBy(['isActive' => true]) as $product){
            $activeWorksheet->setCellValueByColumnAndRow(1,$counter, $product->getTitle());
            $activeWorksheet->setCellValueByColumnAndRow(2,$counter, $product->getDescription());
            $activeWorksheet->setCellValueByColumnAndRow(3,$counter, $product->getCategory()->getTitle());
            $activeWorksheet->setCellValueByColumnAndRow(4,$counter, $product->getPrice());
            $counter++;
        }
        $spreadsheet->getActiveSheet()->setAutoFilter('A1:D'.$counter);

        $writer = new Xlsx($spreadsheet);
        $writer->save('catalog.xlsx');
        $fileName='catalog';
        header('Content-type: application/octet-stream');
        header('Content-Length: ' . filesize($fileName . '.xlsx'));
        header('Content-Disposition: attachment; filename="Catalog.xlsx"');
        readfile($fileName . '.xlsx');
        unlink($fileName . '.xlsx');
        $this->redirect('/');
    }
}
