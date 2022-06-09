<?php

namespace App\Controller\Settings;

use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[
    Route('/settings/users'),
    Security("is_granted('settings.view') and is_granted('settings.users.view')")
]
class UsersController extends AbstractController {

    #[
        Route(
            path: '',
            name: 'settings.users.index',
            methods: ['GET']
        )
    ]
    public function users(
        UserRepository $userRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $pagination = $paginator->paginate($userRepository->findAll(), $request->get('page', 1), 50);
        $nbrOfPages = ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage());
        $firstItem = ($pagination->getCurrentPageNumber() * $pagination->getItemNumberPerPage()) + 1 - $pagination->getItemNumberPerPage();
        $lastItem = $nbrOfPages == $pagination->getCurrentPageNumber() ? $pagination->getTotalItemCount() : $pagination->getCurrentPageNumber() * $pagination->getItemNumberPerPage();
        return $this->render('settings/users/index.html.twig', [
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
            name: 'settings.users.unique.show',
            methods: ['GET']
        )
    ]
    public function showUser(User $user): Response {
        return $this->render('settings/users/show.html.twig', [
            'user' => $user
        ]);
    }
}
