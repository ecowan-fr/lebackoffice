<?php

namespace App\Security;

use App\Entity\User;
use RuntimeException;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use TheNetworg\OAuth2\Client\Provider\AzureResourceOwner;

class AzureAuthenticator extends AbstractSocialAuthenticator {

    protected string $serviceName = 'azure';

    public function getUserFromResourceOwner(ResourceOwnerInterface $azureUser, UserRepository $repository): ?User {
        if (!($azureUser instanceof AzureResourceOwner)) {
            throw new RuntimeException('Expecting AzureResourceOwner as the first parameter');
        }

        $user = $repository->findForOauth('azure', $azureUser->getId());

        return $user;
    }
}
