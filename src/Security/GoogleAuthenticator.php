<?php

namespace App\Security;

use App\Entity\User;
use RuntimeException;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GoogleAuthenticator extends AbstractSocialAuthenticator {

    protected string $serviceName = 'google';
    protected string $redirect = 'home.home';

    public function getUserFromResourceOwner(ResourceOwnerInterface $googleUser, UserRepository $repository, TokenStorageInterface $token): ?User {
        if (!($googleUser instanceof GoogleUser)) {
            throw new RuntimeException('Expecting GoogleUser as the first parameter');
        }

        if (!is_null($token->getToken())) {
            /** @var User */
            $user = $token->getToken()->getUser();
            $user->setGoogleId($googleUser->getId());
            $this->em->flush();

            $this->redirect = 'account.security';
        } else {
            $user = $repository->findForOauth('azure', $googleUser->getId());
        }

        return $user;
    }
}
