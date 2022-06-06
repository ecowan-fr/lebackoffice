<?php

namespace App\Security;

use DateTimeImmutable;
use App\Entity\User as AppUser;
use App\Repository\ConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class UserChecker implements UserCheckerInterface {

    public function __construct(
        private readonly ConfigRepository $configRepository,
        private readonly TranslatorInterface $translator,
        private RequestStack $requestStack,
        private readonly EntityManagerInterface $em
    ) {
    }


    public function checkPreAuth(UserInterface $user): void {
        if (!$user instanceof AppUser) {
            return;
        }

        if (!in_array('login', $user->getRoles()) || ($this->configRepository->findOneBy(['setting' => 'service_mode'])->getValue() && !in_array('settings.service_mode.login', $user->getRoles()))) {
            throw new CustomUserMessageAuthenticationException(
                $this->translator->trans('An authentication exception occurred.', [], 'security')
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void {
        if (!$user instanceof AppUser) {
            return;
        }

        $user->setLastLoginIp($this->requestStack->getCurrentRequest()->getClientIp())
            ->setLastLoginAt(new DateTimeImmutable());
        $this->em->flush();
    }
}
