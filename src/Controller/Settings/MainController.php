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
    Route('/settings/main'),
    IsGranted(new Expression("is_granted('settings.view') and is_granted('settings.main.view')"))
]
class MainController extends AbstractController {

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
            name: 'settings.main.index',
            methods: ['GET']
        )
    ]
    public function index(): Response {
        return $this->render('settings/main/index.html.twig');
    }

    #[
        Route(
            path: '/savelogo',
            name: 'settings.main.saveLogo',
            methods: ['POST']
        ),
        IsGranted('settings.main.edit')
    ]
    public function saveLogo(Request $request, FileUploaderService $fileUploader): RedirectResponse {
        if ($this->isCsrfTokenValid('settings-main-logo', $request->request->get('token'))) {
            if ($request->files->get('structure_logo_url_light')) {
                $type = 'structure_logo_url_light';
                $file = $request->files->get('structure_logo_url_light');
            } elseif ($request->files->get('structure_logo_url_dark')) {
                $type = 'structure_logo_url_dark';
                $file = $request->files->get('structure_logo_url_dark');
            } else {
                $this->addFlash('error', $this->translator->trans('No file was uploaded.', [], 'validators'));
                return $this->redirectToRoute('settings.main.index');
            }

            if (!$file || !in_array($file->getClientMimeType(), ['image/jpeg', 'image/png', 'image/svg+xml'])) {
                $this->addFlash('error', $this->translator->trans('The mime type of the file is invalid ({{ type }}). Allowed mime types are {{ types }}.', ['{{ type }}' => $file->getClientMimeType(), '{{ types }}' => '.png - .jpg - .jpeg - .svg.'], 'validators'));
                return $this->redirectToRoute('settings.main.index');
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
        return $this->redirectToRoute('settings.main.index');
    }

    #[
        Route(
            path: '/deletelogo/{type}',
            name: 'settings.main.deleteLogo',
            requirements: [
                'type' => 'structure_logo_url_light|structure_logo_url_dark'
            ],
            methods: ['GET']
        ),
        IsGranted('settings.main.edit')
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
        return $this->redirectToRoute('settings.main.index');
    }

    #[
        Route(
            path: '/licence',
            name: 'settings.main.licence',
            methods: ['GET']
        )
    ]
    public function licence(Request $request): Response {
        $licence = json_decode($request->getSession()->get('licence'))->infos;
        return $this->render('settings/main/licence.html.twig', compact('licence'));
    }

    #[
        Route(
            path: '/footer',
            name: 'settings.main.footer',
            methods: ['GET']
        )
    ]
    public function footer(): Response {
        return $this->render('settings/main/footer.html.twig');
    }

    #[
        Route(
            path: '/service_mode',
            name: 'settings.main.servicemode',
            methods: ['GET']
        ),
        IsGranted(new Expression("is_granted('settings.main.edit') and is_granted('settings.service_mode.edit')"))
    ]
    public function serviceMode(): Response {
        return $this->render('settings/main/servicemode.html.twig');
    }
}
