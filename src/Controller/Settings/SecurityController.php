<?php

namespace App\Controller\Settings;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

#[
    Route('/settings/security'),
    Security("is_granted('settings.view') and is_granted('settings.security.view')")
]
class SecurityController extends AbstractController {

    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    #[
        Route(
            path: '',
            name: 'settings.security.index',
            methods: ['GET', 'POST']
        )
    ]
    public function security(Request $request): Response {
        return $this->render('settings/security/index.html.twig');
    }

    #[
        Route(
            path: '/webauthn',
            name: 'settings.security.webauthn',
            methods: ['GET']
        )
    ]
    public function webauthn(): Response {
        return $this->render('settings/security/webauthn.html.twig');
    }
}
