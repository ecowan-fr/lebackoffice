<?php

namespace App\Controller\Settings;

use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[
    Route('/settings/users'),
    IsGranted(new Expression("is_granted('settings.view') and is_granted('settings.users.view')"))
]
class UsersController extends AbstractController {

    #[
        Route(
            path: '',
            name: 'settings.users.index',
            methods: ['GET']
        )
    ]
    public function index(): Response {
        return $this->redirectToRoute('settings.index');
    }

    #[
        Route(
            path: '/list',
            name: 'settings.users.list',
            methods: ['GET']
        )
    ]
    public function list(
        UserRepository $userRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $pagination = $paginator->paginate($userRepository->findAll(), $request->get('page', 1), 15);
        $nbrOfPages = ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage());
        $firstItem = ($pagination->getCurrentPageNumber() * $pagination->getItemNumberPerPage()) + 1 - $pagination->getItemNumberPerPage();
        $lastItem = $nbrOfPages == $pagination->getCurrentPageNumber() ? $pagination->getTotalItemCount() : $pagination->getCurrentPageNumber() * $pagination->getItemNumberPerPage();
        return $this->render('settings/users/list.html.twig', [
            "pagination" => $pagination,
            "nbrOfPages" => $nbrOfPages,
            "firstItem" => $firstItem,
            "lastItem" => $lastItem
        ]);
    }

    #[
        Route(
            path: '/azure-ad-sync',
            name: 'settings.users.saad',
            methods: ['GET']
        )
    ]
    public function saad(): Response {
        return $this->render('settings/users/saad.html.twig');
    }

    #[
        Route(
            path: '/show/{id}',
            name: 'settings.users.show',
            methods: ['GET']
        )
    ]
    public function show(User $user): Response {
        return $this->render('settings/users/show.html.twig', [
            'user' => $user
        ]);
    }
}
