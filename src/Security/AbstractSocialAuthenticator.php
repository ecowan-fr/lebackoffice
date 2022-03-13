<?php

namespace App\Security;

use Exception;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Security\Exception\UserOauthNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

abstract class AbstractSocialAuthenticator extends OAuth2Authenticator {

    use TargetPathTrait;

    protected string $serviceName = '';

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        protected EntityManagerInterface $em,
        private readonly RouterInterface $router,
        private readonly UserRepository $userRepository
    ) {
    }

    protected function getClient(): OAuth2ClientInterface {
        return $this->clientRegistry->getClient($this->serviceName);
    }

    protected function getResourceOwnerFromCredentials(AccessToken $credentials): ResourceOwnerInterface {
        return $this->getClient()->fetchUserFromToken($credentials);
    }

    protected function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner, UserRepository $repository): ?User {
        return null;
    }

    public function supports(Request $request): bool {
        if ('' === $this->serviceName) {
            throw new Exception("You must set a \$serviceName property (for instance 'github')");
        }

        return 'oauth.check' === $request->attributes->get('_route') && $request->get('service') === $this->serviceName;
    }

    public function authenticate(Request $request): Passport {
        $client = $this->getClient();
        try {
            $accessToken = $client->getAccessToken();
        } catch (Exception) {
            throw new CustomUserMessageAuthenticationException(
                sprintf("Une erreur est survenue lors de la récupération du token d'accès %s", $this->serviceName)
            );
        }

        try {
            $resourceOwner = $this->getResourceOwnerFromCredentials($accessToken);
        } catch (Exception) {
            throw new CustomUserMessageAuthenticationException(
                sprintf("Une erreur est survenue lors de la communication avec %s", $this->serviceName)
            );
        }

        $user = $this->getUserFromResourceOwner($resourceOwner, $this->userRepository);
        if (null === $user) {
            throw new UserOauthNotFoundException($resourceOwner);
        }

        $userLoader = fn () => $user;

        return new SelfValidatingPassport(
            new UserBadge($user->getUserIdentifier(), $userLoader)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        /** @var Session */
        $session = $request->getSession();

        $session->getFlashBag()->add('success', "Vous êtes authentifié avec " . $this->serviceName);
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('home.home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        /** @var Session */
        $session = $request->getSession();

        if ($exception instanceof UserOauthNotFoundException) {
            $session->getFlashBag()->add('error', "Aucun utilisateur trouvé avec ce compte " . $this->serviceName);
        } else {
            $session->getFlashBag()->add('error', $exception->getMessage());
        }
        return new RedirectResponse($this->router->generate('security.login'));
    }
}
