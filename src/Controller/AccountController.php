<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Service\JWTService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Repository\PublicKeyCredentialSourceRepository;
use App\Repository\PublicKeyCredentialUserEntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[
    Route(
        '/account'
    )
]
class AccountController extends AbstractController {

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
        private readonly PublicKeyCredentialSourceRepository $publicKeyCredentialSourceRepository
    ) {
    }


    #[
        Route(
            '',
            name: 'account.index',
            methods: ['GET']
        )
    ]
    public function index(): Response {
        return $this->render('account/index.html.twig');
    }

    #[
        Route(
            '/security',
            name: 'account.security',
            methods: ['GET', 'POST']
        )
    ]
    public function security(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        PublicKeyCredentialSourceRepository $publicKeyCredentialSourceRepository,
        PublicKeyCredentialUserEntityRepository $publicKeyCredentialUserEntityRepository,
        JWTService $jWTService
    ): Response {
        if ($request->isMethod('POST')) {
            if ($this->isCsrfTokenValid('account.changepassword', $request->request->get('token'))) {
                if ($request->request->get('password_1') === $request->request->get('password_2') && $request->request->get('password_1') != '' &&  !is_null($request->request->get('password_1'))) {
                    try {
                        /** @var User */
                        $user = $this->getUser();
                        $this->userRepository->upgradePassword($user, $userPasswordHasher->hashPassword($user, $request->request->get('password_1')));
                        $this->addFlash('success', $this->translator->trans('Changed password', [], 'account'));
                    } catch (Exception $e) {
                        throw $e;
                    }
                } else {
                    $this->addFlash('error', $this->translator->trans('The two values should be equal.', [], 'validators'));
                }
            } else {
                $this->addFlash('error', $this->translator->trans('Invalid CSRF token.', [], 'security'));
            }
            return $this->redirectToRoute('account.security');
        }

        return $this->render('account/security.html.twig', [
            "tokens" => $publicKeyCredentialSourceRepository->findAllForUserEntity($publicKeyCredentialUserEntityRepository->findOneByUserHandle($this->getUser()->getUserIdentifier()))
        ]);
    }

    #[
        Route(
            '/security/{type}',
            name: 'account.security.setTwofa',
            methods: ['POST'],
            requirements: [
                'type' => 'email'
            ]
        )
    ]
    public function setTwofa(string $type, Request $request) {
        if ($this->isCsrfTokenValid('account.setTwofa', $request->request->get('token'))) {
            if (!is_null($request->request->get($type))) {
                try {
                    /** @var User */
                    $user = $this->getUser();
                    switch ($type) {
                        case 'email':
                            $user->setTwofa_email($request->request->get($type));
                            break;
                    }
                    $this->em->flush();
                    $this->addFlash('success', $this->translator->trans('Settings saved.', [], 'settings'));
                } catch (Exception $e) {
                    throw $e;
                }
            } else {
                $this->addFlash('error', $this->translator->trans('One or more of the given values is invalid.', [], 'validators'));
            }
        } else {
            $this->addFlash('error', $this->translator->trans('Invalid CSRF token.', [], 'security'));
        }
        return $this->redirectToRoute('account.security');
    }

    #[
        Route(
            '/security/backup-code/generate',
            name: 'account.security.backupcode.generate',
            methods: ['GET']
        )
    ]
    public function backupCodeGenerate() {
        try {
            /** @var User */
            $user = $this->getUser();
            $user->clearBackupCode();
            for ($i = 0; $i < 10; $i++) {
                $user->addBackUpCode(random_int(100000, 999999));
            }
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('Backup codes generated', [], 'account'));
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('account.security');
    }

    #[
        Route(
            '/security/backup-code/delete',
            name: 'account.security.backupcode.delete',
            methods: ['GET']
        )
    ]
    public function clearBackupCodes() {
        try {
            /** @var User */
            $user = $this->getUser();
            $user->clearBackupCode();
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('Backup codes removed', [], 'account'));
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('account.security');
    }

    #[
        Route(
            '/security/trusted-device/delete',
            name: 'account.security.trusteddevice.delete',
            methods: ['GET']
        )
    ]
    public function removeTrustedDevice() {
        try {
            /** @var User */
            $user = $this->getUser();
            $user->setTrustedTokenVersion($user->getTrustedTokenVersion() + 1);
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('Trusted devices removed', [], 'account'));
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('account.security');
    }

    #[
        Route(
            '/security/app-two-fa/delete',
            name: 'account.security.apptwofa.delete',
            methods: ['GET']
        )
    ]
    public function removeAppTwoFa() {
        try {
            /** @var User */
            $user = $this->getUser();
            $user->setTotpAppName('')->setTotpSecret('');
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('App removed', [], 'account'));
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('account.security');
    }

    #[
        Route(
            '/security/webauthn/delete/{aaguid}',
            name: 'account.security.webauthn.delete',
            methods: ['GET']
        )
    ]
    public function removeWebAuthnToken(string $aaguid) {
        try {
            $this->publicKeyCredentialSourceRepository->removeByAaguid($aaguid);
            $this->addFlash('success', $this->translator->trans('Token removed', [], 'account'));
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('account.security');
    }
}
