<?php

namespace App\Controller\Settings;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function users(UserRepository $userRepository): Response {
        return $this->render('settings/users/index.html.twig', [
            "users" => $userRepository->findAll()
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
