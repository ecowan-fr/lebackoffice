<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LocaleController extends AbstractController {

    #[Route(path: '/{locale}/{url?}', name: 'locale.change', requirements: ['locale' => 'fr|en', 'url' => '.+'], methods: ['GET'])]
    public function changeLocale(string $locale, $url, Request $request): RedirectResponse {
        $request->getSession()->set('_locale', $locale);

        $go = !is_null($url) ? '/' . $url : $request->headers->get('referer', $this->generateUrl('home.home'));

        return $this->redirect($go);
    }
}
