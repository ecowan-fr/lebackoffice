<?php

namespace App\Security;

use App\Entity\User;
use RuntimeException;
use App\Repository\UserRepository;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class DiscordAuthenticator extends AbstractSocialAuthenticator {

    protected string $serviceName = 'discord';

    public function getUserFromResourceOwner(ResourceOwnerInterface $discordUser, UserRepository $repository): ?User {
        if (!($discordUser instanceof DiscordResourceOwner)) {
            throw new RuntimeException('Expecting DiscordResourceOwner as the first parameter');
        }

        $user = $repository->findForOauth('discord', $discordUser->getId());

        return $user;
    }
}
