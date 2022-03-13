<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/oauth')]
class SocialLoginController extends AbstractController {

    private const SCOPES = [
        'discord' => [],
        'google' => [],
        'github' => ['read:user', 'user:email'],
        'microsoft' => []
    ];

    public function __construct(
        private readonly ClientRegistry $clientRegistry
    ) {
    }

    private function ensureServiceAccepted(string $service) {
        if (!in_array($service, array_keys(self::SCOPES))) {
            $this->addFlash('info', 'Le service ' . $service . ' n\'est pas disponible.');
            throw $this->createAccessDeniedException();
        }
    }

    #[Route('/connect/{service}', name: 'oauth.login', methods: ['GET'])]
    public function connect(string $service): RedirectResponse {
        $this->ensureServiceAccepted($service);

        return $this->clientRegistry->getClient($service)->redirect(self::SCOPES[$service], ['a' => 1]);
    }

    #[Route('/check/{service}', name: 'oauth.check', methods: ['GET'])]
    public function check(): Response {
        return new Response();
    }
}
