<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Repository\PublicKeyCredentialSourceRepository;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Webauthn\Bundle\Repository\PublicKeyCredentialUserEntityRepository;
use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface as TotpTwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;

class LightboxController extends AbstractController {

    public function __construct(
        private readonly TotpAuthenticatorInterface $totpAuthenticator,
        private readonly TranslatorInterface $translator,
        private RequestStack $requestStack,
        private PublicKeyCredentialSourceRepository $publicKeyCredentialSourceRepository,
        private PublicKeyCredentialUserEntityRepository $publicKeyCredentialUserEntityRepository
    ) {
    }

    public function welcome(): string {
        return $this->renderView('home/lightbox/welcome.html.twig');
    }

    public function addAppTwoFa(): string {

        $secret = $this->totpAuthenticator->generateSecret();

        /** @var User $user */
        $user = $this->getUser();
        $user->setTotpSecret($secret);

        /** @var TotpTwoFactorInterface $user */
        $qrCodeContent = $this->totpAuthenticator->getQRContent($user);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($qrCodeContent)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(250)
            ->margin(0)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->build();

        return $this->renderView('account/lightbox/addAppTwoFa.html.twig', [
            'secret' => $secret,
            'qrcode' => $result->getDataUri()
        ]);
    }

    #[
        Route(
            path: '/account/security/app-two-fa/add',
            name: 'account.security.apptwofa.add',
            methods: ['POST']
        )
    ]
    public function saveAppTwoFa(Request $request, EntityManagerInterface $em): RedirectResponse {
        if ($this->isCsrfTokenValid('account.addAppTwoFa', $request->request->get('token'))) {
            if ($request->request->get('secret') && $request->request->get('code') && $request->request->get('name')) {
                try {
                    /** @var User $user */
                    $user = $this->getUser();
                    $user->setTotpAppName($request->request->get('name'))->setTotpSecret($request->request->get('secret'));

                    /** @var TotpTwoFactorInterface $user */
                    if ($this->totpAuthenticator->checkCode($user, $request->request->get('code'))) {
                        $em->flush();
                        $this->addFlash('success', $this->translator->trans('New app added', [], 'account'));
                    } else {
                        $this->addFlash('error', $this->translator->trans('code_invalid', [], 'SchebTwoFactorBundle'));
                    }
                } catch (Exception $e) {
                    throw $e;
                }
            } else {
                $this->addFlash('error', $this->translator->trans('Not all fields are filled in', [], 'global'));
            }
        } else {
            $this->addFlash('error', $this->translator->trans('Invalid CSRF token.', [], 'security'));
        }
        return $this->redirectToRoute('account.security');
    }

    public function webauthn() {
        $aaguid = $this->requestStack->getCurrentRequest()->get('aaguid');
        $token = $this->publicKeyCredentialSourceRepository->findOneByAaguid($aaguid);
        return $this->renderView('account/lightbox/viewTokenForWebAuthn.html.twig', [
            "token" => $token
        ]);
    }
}
