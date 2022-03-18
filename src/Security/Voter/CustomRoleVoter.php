<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CustomRoleVoter extends Voter {
    protected function supports(string $attribute, $subject): bool {
        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        return in_array($attribute, $user->getRoles());
    }
}
