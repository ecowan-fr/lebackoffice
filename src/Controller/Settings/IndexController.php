<?php

namespace App\Controller\Settings;

use Exception;
use App\Repository\ConfigRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;

#[
    Route('/settings'),
    IsGranted('settings.view')
]
class IndexController extends AbstractController {

    #[
        Route(
            path: '',
            name: 'settings.index',
            methods: ['GET']
        )
    ]
    public function index(): Response {
        return $this->render('settings/index.html.twig');
    }

    #[
        Route(
            path: '/save-setting',
            name: 'settings.savesetting',
            methods: ['POST']
        ),
        IsGranted(
            new Expression("
            is_granted('settings.edit') or
            is_granted('settings.security.edit') or
            is_granted('settings.service_mode.edit')")
        )
    ]
    public function savesetting(Request $request, ConfigRepository $configRepository, TranslatorInterface $translator): RedirectResponse {
        if ($this->isCsrfTokenValid('settings', $request->request->get('token'))) {
            try {
                $configRepository->saveMultiple($request->request->all());
                $this->addFlash('success', $translator->trans('Settings saved.', [], 'settings'));
            } catch (Exception $e) {
                $this->addFlash('error', $translator->trans('Unable to save settings.', [], 'settings'));
                $this->addFlash('error', $e->getMessage());
            }
        } else {
            $this->addFlash('error', $translator->trans('Invalid CSRF token.', [], 'security'));
        }
        return $this->redirect($request->headers->get('referer', $this->generateUrl('settings.index')));
    }
}
