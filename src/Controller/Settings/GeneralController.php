<?php

namespace App\Controller\Settings;

use Exception;
use App\Repository\ConfigRepository;
use App\Service\FileUploaderService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[
    Route('/settings/general'),
    IsGranted('settings.view')
]
class GeneralController extends AbstractController {

    private $defaultLogoLight = "/images/logo/logo-lebackoffice-noir.svg";
    private $defaultLogoDark = "/images/logo/logo-lebackoffice-blanc.svg";

    public function __construct(
        private readonly ConfigRepository $configRepository,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[
        Route(
            path: '',
            name: 'settings.general.index',
            methods: ['GET']
        )
    ]
    public function index(): Response {
        return $this->redirectToRoute('settings.index');
    }

    #[
        Route(
            path: '/structure',
            name: 'settings.general.structure',
            methods: ['GET']
        )
    ]
    public function structure(): Response {
        return $this->render('settings/general/structure.html.twig');
    }

    #[
        Route(
            path: '/save-logo',
            name: 'settings.general.savelogo',
            methods: ['POST']
        ),
        IsGranted('settings.edit')
    ]
    public function savelogo(Request $request, FileUploaderService $fileUploader): RedirectResponse {
        if ($this->isCsrfTokenValid('settings-main-logo', $request->request->get('token'))) {
            if ($request->files->get('structure_logo_url_light')) {
                $type = 'structure_logo_url_light';
                $file = $request->files->get('structure_logo_url_light');
            } elseif ($request->files->get('structure_logo_url_dark')) {
                $type = 'structure_logo_url_dark';
                $file = $request->files->get('structure_logo_url_dark');
            } else {
                $this->addFlash('error', $this->translator->trans('No file was uploaded.', [], 'validators'));
                return $this->redirectToRoute('settings.general.structure');
            }

            if (!$file || !in_array($file->getClientMimeType(), ['image/jpeg', 'image/png', 'image/svg+xml'])) {
                $this->addFlash('error', $this->translator->trans('The mime type of the file is invalid ({{ type }}). Allowed mime types are {{ types }}.', ['{{ type }}' => $file->getClientMimeType(), '{{ types }}' => '.png - .jpg - .jpeg - .svg.'], 'validators'));
                return $this->redirectToRoute('settings.general.structure');
            }

            try {
                $filename = $fileUploader->upload($file);
                $this->configRepository->save($type, "/" . $fileUploader->getTargetDirectory() . $filename);
                $this->addFlash('success', $this->translator->trans('The file "%n" has been saved.', ['%n' => $filename], 'settings'));
            } catch (Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        } else {
            $this->addFlash('error', $this->translator->trans('Invalid CSRF token.', [], 'security'));
        }
        return $this->redirectToRoute('settings.general.structure');
    }

    #[
        Route(
            path: '/delete-logo/{type}',
            name: 'settings.general.deletelogo',
            requirements: [
                'type' => 'structure_logo_url_light|structure_logo_url_dark'
            ],
            methods: ['GET']
        ),
        IsGranted('settings.edit')
    ]
    public function deleteLogo(string $type, Filesystem $filesystem): RedirectResponse {
        try {
            $value = $type === 'structure_logo_url_light' ? $this->defaultLogoLight : $this->defaultLogoDark;
            $filesystem->remove($this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . 'public' . str_replace('/', DIRECTORY_SEPARATOR, $this->configRepository->findOneBy(['setting' => $type])->getValue()));
            $this->configRepository->save($type, $value);
            $this->addFlash('success', $this->translator->trans('Settings saved.', [], 'settings'));
        } catch (Exception $e) {
            $this->addFlash('error', $this->translator->trans('Unable to save settings.', [], 'settings'));
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('settings.general.structure');
    }

    #[
        Route(
            path: '/licence',
            name: 'settings.general.licence',
            methods: ['GET']
        )
    ]
    public function licence(Request $request): Response {
        $licence = json_decode($request->getSession()->get('licence'))->infos;
        return $this->render('settings/general/licence.html.twig', compact('licence'));
    }

    #[
        Route(
            path: '/service-mode',
            name: 'settings.general.servicemode',
            methods: ['GET']
        ),
        IsGranted(new Expression("is_granted('settings.edit') and is_granted('settings.service_mode.edit')"))
    ]
    public function serviceMode(): Response {
        return $this->render('settings/general/servicemode.html.twig');
    }
}
