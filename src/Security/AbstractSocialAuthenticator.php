<?php

namespace App\Security;

use Exception;
use App\Entity\User;
use App\Repository\ConfigRepository;
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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractSocialAuthenticator extends OAuth2Authenticator {

    use TargetPathTrait;

    protected string $serviceName = '';

    protected string $redirect = 'home.home';

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        protected EntityManagerInterface $em,
        private readonly RouterInterface $router,
        private readonly UserRepository $userRepository,
        private readonly TranslatorInterface $translator,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly ConfigRepository $configRepository
    ) {
    }

    protected function getClient(): OAuth2ClientInterface {
        return $this->clientRegistry->getClient($this->serviceName);
    }

    protected function getResourceOwnerFromCredentials(AccessToken $credentials): ResourceOwnerInterface {
        return $this->getClient()->fetchUserFromToken($credentials);
    }

    protected function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner, UserRepository $repository, TokenStorageInterface $token): ?User {
        return null;
    }

    public function supports(Request $request): bool {
        if ('' === $this->serviceName) {
            throw new Exception("You must set a \$serviceName property.");
        }

        return 'oauth.check' === $request->attributes->get('_route') && $request->get('service') === $this->serviceName;
    }

    public function authenticate(Request $request): Passport {
        if (!$this->configRepository->findOneBy(['setting' => 'login_oauth_' . $this->serviceName])->getValue()) {
            throw new CustomUserMessageAuthenticationException(
                $this->translator->trans('An authentication exception occurred.', ['%s' => $this->serviceName], 'security')
            );
        }
        $client = $this->getClient();
        try {
            $accessToken = $client->getAccessToken();
        } catch (Exception) {
            throw new CustomUserMessageAuthenticationException(
                $this->translator->trans('An error occurred while retrieving the access token %s', ['%s' => $this->serviceName], 'security')
            );
        }

        try {
            $resourceOwner = $this->getResourceOwnerFromCredentials($accessToken);
        } catch (Exception) {
            throw new CustomUserMessageAuthenticationException(
                $this->translator->trans('An error occurred while communicating with %s', ['%s' => $this->serviceName], 'security')
            );
        }

        $user = $this->getUserFromResourceOwner($resourceOwner, $this->userRepository, $this->tokenStorage);

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

        $session->getFlashBag()->add('success', $this->translator->trans('You are authenticated with %s', ['%s' => $this->serviceName], 'security'));

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName) && $this->redirect === 'home.home') {
            dump('targetpath');
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate($this->redirect));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        /** @var Session */
        $session = $request->getSession();

        if ($exception instanceof UserOauthNotFoundException) {
            $session->getFlashBag()->add('error', $this->translator->trans('No users found with this account %s', ['%s' => $this->serviceName], 'security'));
        } else {
            $session->getFlashBag()->add('error', $exception->getMessage());
        }
        return new RedirectResponse($this->router->generate('security.login'));
    }
}
