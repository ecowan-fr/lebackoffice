<?php

namespace App\Controller\Settings;

use Exception;
use App\Repository\ConfigRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
            path: '/savesetting',
            name: 'settings.savesetting',
            methods: ['POST']
        ),
        Security("is_granted('settings.main.edit') or is_granted('settings.security.edit')")
    ]
    public function saveSetting(Request $request, ConfigRepository $configRepository, TranslatorInterface $translator): RedirectResponse {
        if ($this->isCsrfTokenValid('settings', $request->request->get('token'))) {
            try {
                foreach ($request->request->all() as $key => $value) {
                    if (
                        $key != 'token' && (
                            (str_contains('main', $value) and !$this->isGranted('settings.main.edit')) ||
                            (str_contains('security', $value) and !$this->isGranted('settings.security.edit'))
                        )
                    ) {
                        throw $this->createAccessDeniedException();
                    }
                }

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
