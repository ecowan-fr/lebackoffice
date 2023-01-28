<?php

namespace App\Security;

use App\Entity\User;
use RuntimeException;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GithubAuthenticator extends AbstractSocialAuthenticator {

    protected string $serviceName = 'github';
    protected string $redirect = 'home.home';

    public function getUserFromResourceOwner(ResourceOwnerInterface $githubUser, UserRepository $repository, TokenStorageInterface $token): ?User {
        if (!($githubUser instanceof GithubResourceOwner)) {
            throw new RuntimeException('Expecting GithubResourceOwner as the first parameter');
        }

        if (!is_null($token->getToken())) {
            /** @var User */
            $user = $token->getToken()->getUser();
            $user->setGithubId($githubUser->getId());
            $this->em->flush();

            $this->redirect = 'account.security';
        } else {
            $user = $repository->findForOauth($this->serviceName, $githubUser->getId());
        }

        return $user;
    }
}
