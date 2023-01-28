<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/oauth')]
class SocialLoginController extends AbstractController {

    private const SCOPES = [
        'discord' => ['identify'],
        'google' => [],
        'github' => ['user'],
        'azure' => ['profile']
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

    #[Route(path: '/connect/{service}', name: 'oauth.login', methods: ['GET'])]
    public function connect(string $service): RedirectResponse {
        $this->ensureServiceAccepted($service);

        return $this->clientRegistry->getClient($service)->redirect(self::SCOPES[$service], ['a' => 1]);
    }

    #[Route(path: '/check/{service}', name: 'oauth.check', methods: ['GET'])]
    public function check(): Response {
        return new Response();
    }

    #[Route(path: '/unlink/{service}', name: 'oauth.disconnect', methods: ['GET'])]
    public function disconnect(string $service, EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse {
        $this->ensureServiceAccepted($service);

        /** @var User */
        $user = $this->getUser();
        $method = 'set' . ucfirst($service) . 'Id';
        $user->$method(null);
        $em->flush();
        $this->addFlash('success', $translator->trans('Your account has been successfully unlinked from %n', ['%n' => $service], 'account'));

        return $this->redirectToRoute('account.security');
    }
}
