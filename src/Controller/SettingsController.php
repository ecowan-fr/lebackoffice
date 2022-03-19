<?php

namespace App\Controller;

use Exception;
use App\Service\FileUploaderService;
use App\Repository\ConfigRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[
    Route('/settings'),
    IsGranted('settings.view')
]
class SettingsController extends AbstractController {

    private $defaultLogoLight = "/images/logo/logo-lebackoffice-noir.svg";
    private $defaultLogoDark = "/images/logo/logo-lebackoffice-blanc.svg";

    public function __construct(
        private readonly ConfigRepository $configRepository,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '', name: 'settings.index', methods: ['GET'])]
    public function index(): Response {
        return $this->render('settings/index.html.twig');
    }

    #[
        Route(
            path: '/main',
            name: 'settings.main',
            methods: ['GET', 'POST']
        ),
        IsGranted('settings.main.view')
    ]
    public function main(Request $request): Response {
        if ($request->isMethod('POST')) {
            if (!$this->isGranted('settings.main.edit')) {
                throw $this->createAccessDeniedException();
            }

            if ($this->isCsrfTokenValid('settings-main-name', $request->request->get('token'))) {
                $structure_name = $request->get('structure_name');
                $structure_type = $request->get('structure_type');
                $structure_siret = $request->get('structure_siret');
                $structure_siren = $request->get('structure_siren');
                $structure_rna = strtoupper($request->get('structure_rna'));
                $structure_vat = strtoupper($request->get('structure_vat'));
                $structure_eori = strtoupper($request->get('structure_eori'));
                $structure_ics = strtoupper($request->get('structure_ics'));
                $structure_email = $request->get('structure_email');
                $structure_tel = $request->get('structure_tel');
                $structure_adress = $request->get('structure_adress');

                if ($structure_name === '' || $structure_type === '' || $structure_email === '') {
                    $this->addFlash('error', $this->translator->trans('The Name, Type and Email fields of the structure are required.', [], 'settings'));
                    return $this->redirectToRoute('settings.main');
                }

                $data = [
                    "structure.name" => $structure_name,
                    "structure.type" => $structure_type,
                    "structure.siret" => $structure_siret,
                    "structure.siren" => $structure_siren,
                    "structure.rna" => $structure_rna,
                    "structure.vat" => $structure_vat,
                    "structure.eori" => $structure_eori,
                    "structure.ics" => $structure_ics,
                    "structure.email" => $structure_email,
                    "structure.tel" => $structure_tel,
                    "structure.adress" => $structure_adress
                ];

                try {
                    $this->configRepository->saveMultiple($data);
                    $this->addFlash('success', $this->translator->trans('Settings saved.', [], 'settings'));
                } catch (Exception $e) {
                    $this->addFlash('error', $this->translator->trans('Unable to save settings.', [], 'settings'));
                    throw $e;
                }
            } else {
                $this->addFlash('error', $this->translator->trans('Invalid CSRF token.', [], 'security'));
            }
            return $this->redirectToRoute('settings.main');
        }
        return $this->render('settings/main.html.twig');
    }

    #[
        Route(
            path: '/main/savelogocustom',
            name: 'settings.saveLogoCustom',
            methods: ['POST']
        ),
        IsGranted('settings.main.edit')
    ]
    public function saveLogoCustom(Request $request): RedirectResponse {
        if ($this->isCsrfTokenValid('settings-main-logo-custom', $request->request->get('token'))) {
            $custom = $request->request->get('structure_logo_custom');
            if ($custom != '1' && $custom != '0') {
                $this->addFlash('error', $this->translator->trans('This value should be of type {{ type }}.', ['{{ type }}' => '0 - 1'], 'validators'));
                return $this->redirectToRoute('settings.main');
            }
            try {
                $this->configRepository->save('structure.logo.custom', $custom);
                $this->addFlash('success', $this->translator->trans('Settings saved.', [], 'settings'));
            } catch (Exception $e) {
                $this->addFlash('error', $this->translator->trans('Unable to save settings.', [], 'settings'));
            }
        } else {
            $this->addFlash('error', $this->translator->trans('Invalid CSRF token.', [], 'security'));
        }
        return $this->redirectToRoute('settings.main');
    }

    #[
        Route(
            path: '/main/savelogo',
            name: 'settings.saveLogo',
            methods: ['POST']
        ),
        IsGranted('settings.main.edit')
    ]
    public function saveLogo(Request $request, FileUploaderService $fileUploader): RedirectResponse {
        if ($this->isCsrfTokenValid('settings-main-logo', $request->request->get('token'))) {
            if ($request->files->get('structure_logo_url_light')) {
                $type = 'structure.logo.url.light';
                $file = $request->files->get('structure_logo_url_light');
            } elseif ($request->files->get('structure_logo_url_dark')) {
                $type = 'structure.logo.url.dark';
                $file = $request->files->get('structure_logo_url_dark');
            } else {
                $this->addFlash('error', $this->translator->trans('No file was uploaded.', [], 'validators'));
                return $this->redirectToRoute('settings.main');
            }

            if (!$file || !in_array($file->getClientMimeType(), ['image/jpeg', 'image/png', 'image/svg+xml'])) {
                $this->addFlash('error', $this->translator->trans('The mime type of the file is invalid ({{ type }}). Allowed mime types are {{ types }}.', ['{{ type }}' => $file->getClientMimeType(), '{{ types }}' => '.png - .jpg - .jpeg - .svg.'], 'validators'));
                return $this->redirectToRoute('settings.main');
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
        return $this->redirectToRoute('settings.main');
    }

    #[
        Route(
            path: '/main/deletelogo/{type}',
            name: 'settings.deleteLogo',
            requirements: [
                'type' => 'structure.logo.url.light|structure.logo.url.dark'
            ],
            methods: ['GET']
        ),
        IsGranted('settings.main.edit')
    ]
    public function deleteLogo(string $type, Filesystem $filesystem) {
        try {
            $value = $type === 'structure.logo.url.light' ? $this->defaultLogoLight : $this->defaultLogoDark;
            $filesystem->remove($this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . 'public' . str_replace('/', DIRECTORY_SEPARATOR, $this->configRepository->findOneBy(['setting' => $type])->getValue()));
            $this->configRepository->save($type, $value);
            $this->addFlash('success', $this->translator->trans('Settings saved.', [], 'settings'));
        } catch (Exception $e) {
            $this->addFlash('error', $this->translator->trans('Unable to save settings.', [], 'settings'));
        }
        return $this->redirectToRoute('settings.main');
    }
}
