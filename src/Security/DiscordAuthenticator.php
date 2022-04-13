<?php

namespace App\Security;

use App\Entity\User;
use RuntimeException;
use App\Repository\UserRepository;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DiscordAuthenticator extends AbstractSocialAuthenticator {

    protected string $serviceName = 'discord';
    protected string $redirect = 'home.home';

    public function getUserFromResourceOwner(ResourceOwnerInterface $discordUser, UserRepository $repository, TokenStorageInterface $token): ?User {
        if (!($discordUser instanceof DiscordResourceOwner)) {
            throw new RuntimeException('Expecting DiscordResourceOwner as the first parameter');
        }

        if (!is_null($token->getToken())) {
            /** @var User */
            $user = $token->getToken()->getUser();
            $user->setDiscordId($discordUser->getId());
            $this->em->flush();

            $this->redirect = 'account.security';
        } else {
            $user = $repository->findForOauth('discord', $discordUser->getId());
        }

        return $user;
    }
}
