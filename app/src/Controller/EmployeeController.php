<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Employee;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Repository\EmployeeRepository;
use App\Repository\UserRepository;
use App\Validation\CategoryValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Config\Framework\Assets\PackageConfig;

/**
 * Class PostController
 * @package App\Controller
 * @Route("/employee", name="post_api")
 */
class EmployeeController extends AbstractController
{
    /**
     * @return JsonResponse
     * @Route("/list", name="list", methods={"GET"})
     */
    public function getListEmployee(Request $request, EmployeeRepository $repository)
    {
        $sort = $request->query->get('sort') ?? [];
        $nextSort = 'ASC';
        switch ($sort){
            case 'ASC':
                $nextSort = "DESC";
                break;
            case 'DESC':
                $nextSort = null;
                break;
            case null:
                $nextSort = "ASC";
                break;
            default:
                $sort = null;
                break;
        }
        if ($sort){
            $data = $this->transformData($repository->findBy([], ['lastName' => $sort]));
        } else {
            $sort = 'ASC';
            $data = $this->transformData($repository->findAll());
        }
        return $this->render('employees.html.twig', [
            'employees' => $data,
            'sort' => $nextSort
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/view/{employee}", name="view_one_employee", methods={"GET"})
     */
    public function viewEmployee(Employee $employee, EntityManagerInterface $em)
    {
//        $em->remove($employee);
//        $em->flush();
        return $this->redirect('/employee/list');
    }

    /**
     * @return JsonResponse
     * @Route("/remove/{employee}", name="remove_employee", methods={"POST"})
     */
    public function deleteEmployee(Employee $employee, EntityManagerInterface $em)
    {
        $em->remove($employee);
        $em->flush();
        return $this->redirect('/employee/list');
    }


    /**
     * @return JsonResponse
     * @Route("/add", name="view_employee", methods={"GET"})
     */
    public function addEmployee(Request $request, EmployeeRepository $repository, CityRepository $cityRepository)
    {
        $city = [];
        foreach ($cityRepository->findAll() as $item){
            $city[] = $item->getName();
        }
        return $this->render('addForm.html.twig',['city' => $city]);
    }

    private function transformData(?array $data){
        $result = [];
        /** @var Employee $item */
        foreach ($data as $item){
            $result[] = [
                'fullName' => $item->getLastName(),
                'date' => $item->getDateOfBirth()->format('Y-m-d'),
                'id' => $item->getId()
            ];
        }
        return $result;
    }
}