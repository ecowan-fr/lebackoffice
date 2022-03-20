<?php

namespace App\Controller\Settings;

use Exception;
use App\Repository\ConfigRepository;
use App\Service\FileUploaderService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[
    Route('/settings/main'),
    Security("is_granted('settings.view') and is_granted('settings.main.view')")
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
            methods: ['GET', 'POST']
        )
    ]
    public function index(Request $request): Response {
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
                    return $this->redirectToRoute('settings.main.index');
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
            return $this->redirectToRoute('settings.main.index');
        }
        return $this->render('settings/main/index.html.twig');
    }

    #[
        Route(
            path: '/savelogocustom',
            name: 'settings.main.saveLogoCustom',
            methods: ['POST']
        ),
        IsGranted('settings.main.edit')
    ]
    public function saveLogoCustom(Request $request): RedirectResponse {
        if ($this->isCsrfTokenValid('settings-main-logo-custom', $request->request->get('token'))) {
            $custom = $request->request->get('structure_logo_custom');
            if ($custom != '1' && $custom != '0') {
                $this->addFlash('error', $this->translator->trans('This value should be of type {{ type }}.', ['{{ type }}' => '0 - 1'], 'validators'));
                return $this->redirectToRoute('settings.main.index');
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
        return $this->redirectToRoute('settings.main.index');
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
                $type = 'structure.logo.url.light';
                $file = $request->files->get('structure_logo_url_light');
            } elseif ($request->files->get('structure_logo_url_dark')) {
                $type = 'structure.logo.url.dark';
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
                'type' => 'structure.logo.url.light|structure.logo.url.dark'
            ],
            methods: ['GET']
        ),
        IsGranted('settings.main.edit')
    ]
    public function deleteLogo(string $type, Filesystem $filesystem): RedirectResponse {
        try {
            $value = $type === 'structure.logo.url.light' ? $this->defaultLogoLight : $this->defaultLogoDark;
            $filesystem->remove($this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . 'public' . str_replace('/', DIRECTORY_SEPARATOR, $this->configRepository->findOneBy(['setting' => $type])->getValue()));
            $this->configRepository->save($type, $value);
            $this->addFlash('success', $this->translator->trans('Settings saved.', [], 'settings'));
        } catch (Exception $e) {
            $this->addFlash('error', $this->translator->trans('Unable to save settings.', [], 'settings'));
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
            methods: ['GET', 'POST']
        )
    ]
    public function footer(Request $request) {
        if ($request->isMethod('POST')) {
            if (!$this->isGranted('settings.main.edit')) {
                throw $this->createAccessDeniedException();
            }

            if ($this->isCsrfTokenValid('settings-main-footer-type', $request->request->get('token'))) {
                $typeText = null;
                if ($request->request->get('footer_left_type')) {
                    $type = 'footer.left.type';
                    $data = $request->request->get('footer_left_type');
                    if ($data === 'text' && !is_null($request->request->get('footer_left_text'))) {
                        $typeText = 'footer.left.text';
                        $text = $request->request->get('footer_left_text');
                    }
                } elseif ($request->request->get('footer_center_type')) {
                    $type = 'footer.center.type';
                    $data = $request->request->get('footer_center_type');
                    if ($data === 'text' && !is_null($request->request->get('footer_center_text'))) {
                        $typeText = 'footer.center.text';
                        $text = $request->request->get('footer_center_text');
                    }
                } elseif ($request->request->get('footer_right_type')) {
                    $type = 'footer.right.type';
                    $data = $request->request->get('footer_right_type');
                    if ($data === 'text' && !is_null($request->request->get('footer_right_text'))) {
                        $typeText = 'footer.right.text';
                        $text = $request->request->get('footer_right_text');
                    }
                } else {
                    $this->addFlash('error', $this->translator->trans('One or more of the given values is invalid.', [], 'validators'));
                    return $this->redirectToRoute('settings.main.footer');
                }

                try {
                    $this->configRepository->save($type, $data);
                    if ($data === 'text' && !is_null($typeText)) {
                        $this->configRepository->save($typeText, $text);
                    }
                    $this->addFlash('success', $this->translator->trans('Settings saved.', [], 'settings'));
                } catch (Exception $e) {
                    $this->addFlash('error', $this->translator->trans('Unable to save settings.', [], 'settings'));
                    throw $e;
                }
            } else {
                $this->addFlash('error', $this->translator->trans('Invalid CSRF token.', [], 'security'));
            }
            return $this->redirectToRoute('settings.main.footer');
        }

        return $this->render('settings/main/footer.html.twig');
    }

    #[
        Route(
            path: '/footer/active',
            name: 'settings.main.footeractive',
            methods: ['POST']
        ),
        IsGranted('settings.main.edit')
    ]
    public function setFooterActive(Request $request): RedirectResponse {
        if ($this->isCsrfTokenValid('settings-footer-active', $request->request->get('token'))) {
            $active = $request->request->get('footer_active');
            if ($active != '1' && $active != '0') {
                $this->addFlash('error', $this->translator->trans('This value should be of type {{ type }}.', ['{{ type }}' => '0 - 1'], 'validators'));
                return $this->redirectToRoute('settings.main.footer');
            }
            try {
                $this->configRepository->save('footer.active', $active);
                $this->addFlash('success', $this->translator->trans('Settings saved.', [], 'settings'));
            } catch (Exception $e) {
                $this->addFlash('error', $this->translator->trans('Unable to save settings.', [], 'settings'));
            }
        } else {
            $this->addFlash('error', $this->translator->trans('Invalid CSRF token.', [], 'security'));
        }
        return $this->redirectToRoute('settings.main.footer');
    }
}
