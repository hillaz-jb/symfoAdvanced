<?php

namespace App\Controller;

use App\Form\SearchUserType;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private UserRepository $userRepository;
    private PaginatorInterface $paginator;

    /**
     * @param UserRepository $userRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(UserRepository $userRepository, PaginatorInterface $paginator)
    {
        $this->userRepository = $userRepository;
        $this->paginator = $paginator;
    }


    #[Route('/', name: 'home')]
    public function index(Request $request): Response //Request pour acceder au super variables get post session
    {
        $form = $this->createForm(SearchUserType::class);

        $form->handleRequest($request);

        $qb = $this->userRepository->getQbAll();

        if ($form->isSubmitted() && $form->isValid() && $form->isValid()) {

            $data = $form->get('email')->getData();
            dump($data);
            $qb->where('user.email LIKE :data')
                ->setParameter(':data',"%$data%");

        }

        $pagination = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 4);

        $user = $this->getUser();
        dump($user);
        /*if ($user === null){
            return $this->redirectToRoute('app_login');
        }*/

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'pagination' => $pagination,
            'form' => $form->createView(),
        ]);
    }
}
