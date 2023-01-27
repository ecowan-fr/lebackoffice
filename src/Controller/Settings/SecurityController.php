<?php

namespace App\Controller\Settings;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[
    Route('/settings/security'),
    IsGranted(new Expression("is_granted('settings.view') and is_granted('settings.security.view')"))
]
class SecurityController extends AbstractController {

    #[
        Route(
            path: '',
            name: 'settings.security.index',
            methods: ['GET']
        )
    ]
    public function index(): Response {
        return $this->redirectToRoute('settings.index');
    }

    #[
        Route(
            path: '/login-system',
            name: 'settings.security.loginsystem',
            methods: ['GET']
        )
    ]
    public function loginsystem(): Response {
        return $this->render('settings/security/login_system.html.twig');
    }
}
