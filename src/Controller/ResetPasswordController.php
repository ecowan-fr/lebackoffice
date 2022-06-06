<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\ConfigRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

#[Route(path: '/reset-password')]
class ResetPasswordController extends AbstractController {

    use ResetPasswordControllerTrait;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MailerInterface $mailer,
        private readonly TranslatorInterface $translator,
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly ConfigRepository $configRepository
    ) {
    }

    #[Route(path: '', name: 'security.resetpassword.request', methods: ['GET', 'POST'])]
    public function request(Request $request, AuthenticationUtils $authenticationUtils): Response {
        if (!$this->configRepository->findOneBy(['setting' => 'login_password'])->getValue()) {
            return $this->redirectToRoute('security.login');
        }

        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData()
            );
        }
        $form->get('email')->setData($authenticationUtils->getLastUsername());

        return $this->render('security/reset_password/request.html.twig', [
            'requestForm' => $form->createView()
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData): RedirectResponse {
        $user = $this->userRepository->findOneBy(['email' => $emailFormData,]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('security.resetpassword.checkemail');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('warning', sprintf(
                '%s - %s',
                $this->translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE, [], 'ResetPasswordBundle'),
                $this->translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('security.resetpassword.checkemail');
        }

        $email = (new TemplatedEmail())
            ->from(new Address($_ENV['MAILER_FROM_EMAIL'], $_ENV['MAILER_FROM_NAME']))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('emails/reset-password.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        $this->mailer->send($email);

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        $this->addFlash('success', $this->translator->trans('Email sent.', [], 'global'));

        return $this->redirectToRoute('security.resetpassword.checkemail');
    }

    #[Route(path: '/check-email', name: 'security.resetpassword.checkemail', methods: ['GET'])]
    public function checkEmail(): Response {
        if (!$this->configRepository->findOneBy(['setting' => 'login_password'])->getValue()) {
            return $this->redirectToRoute('security.login');
        }

        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('security/reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    #[Route(path: '/reset/{token}', name: 'security.resetpassword.resetpassword', methods: ['GET', 'POST'])]
    public function reset(Request $request, UserPasswordHasherInterface $userPasswordHasher, string $token = null): Response {
        if (!$this->configRepository->findOneBy(['setting' => 'login_password'])->getValue()) {
            return $this->redirectToRoute('security.login');
        }

        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('security.resetpassword.resetpassword');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('error', sprintf(
                '%s - %s',
                $this->translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                $this->translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('security.resetpassword.request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            $this->userRepository->upgradePassword($user, $userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData()));

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            $this->addFlash('success', $this->translator->trans('Password changed successfully.'));

            return $this->redirectToRoute('security.login');
        }

        return $this->render('security/reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }
}
