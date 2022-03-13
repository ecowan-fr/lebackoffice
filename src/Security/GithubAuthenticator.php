<?php

namespace App\Security;

use App\Entity\User;
use RuntimeException;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class GithubAuthenticator extends AbstractSocialAuthenticator {

    protected string $serviceName = 'github';

    public function getUserFromResourceOwner(ResourceOwnerInterface $githubUser, UserRepository $repository): ?User {
        if (!($githubUser instanceof GithubResourceOwner)) {
            throw new RuntimeException('Expecting GithubResourceOwner as the first parameter');
        }
        $user = $repository->findForOauth('github', $githubUser->getId());
        return $user;
    }
}
