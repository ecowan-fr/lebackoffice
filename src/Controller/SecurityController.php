<?php

namespace App\Controller;

use LogicException;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
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

class SecurityController extends AbstractController {

    use ResetPasswordControllerTrait;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MailerInterface $mailer,
        private readonly TranslatorInterface $translator,
        private readonly ResetPasswordHelperInterface $resetPasswordHelper
    ) {
    }

    #[Route(path: '/login', name: 'security.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('home.home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'security.logout', methods: ['GET'])]
    public function logout(): void {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/reset-password', name: 'security.resetpassword.request', methods: ['GET', 'POST'])]
    public function request(Request $request): Response {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData()
            );
        }

        return $this->render('security/reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
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
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            $this->addFlash('warning', sprintf(
                '%s - %s',
                $this->translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE, [], 'ResetPasswordBundle'),
                $this->translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('security.resetpassword.checkemail');
        }

        $email = (new TemplatedEmail())
            ->from(new Address('noreply@ecowan.fr', 'Lebackoffice'))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('security/reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        $this->mailer->send($email);

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        $this->addFlash('success', $this->translator->trans('Email sent.'));

        return $this->redirectToRoute('security.resetpassword.checkemail');
    }

    #[Route('/reset-password/check-email', name: 'security.resetpassword.checkemail', methods: ['GET'])]
    public function checkEmail(): Response {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('security/reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    #[Route('/reset-password/reset/{token}', name: 'security.resetpassword.resetpassword', methods: ['GET', 'POST'])]
    public function reset(Request $request, UserPasswordHasherInterface $userPasswordHasher, string $token = null): Response {
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
