<?php

namespace App\Security;

use App\Entity\User;
use RuntimeException;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use TheNetworg\OAuth2\Client\Provider\AzureResourceOwner;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AzureAuthenticator extends AbstractSocialAuthenticator {

    protected string $serviceName = 'azure';
    protected string $redirect = 'home.home';

    public function getUserFromResourceOwner(ResourceOwnerInterface $azureUser, UserRepository $repository, TokenStorageInterface $token): ?User {
        if (!($azureUser instanceof AzureResourceOwner)) {
            throw new RuntimeException('Expecting AzureResourceOwner as the first parameter');
        }

        if (!is_null($token->getToken())) {
            /** @var User */
            $user = $token->getToken()->getUser();
            $user->setAzureId($azureUser->getId());
            $this->em->flush();

            $this->redirect = 'account.security';
        } else {
            $user = $repository->findForOauth('azure', $azureUser->getId());
        }

        return $user;
    }
}
