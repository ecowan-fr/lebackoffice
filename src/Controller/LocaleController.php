<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LocaleController extends AbstractController {

    #[Route(path: '/{locale}', name: 'locale.change', requirements: ['locale' => 'fr|en'], methods: ['GET'])]
    public function fr(string $locale, Request $request): RedirectResponse {
        $request->getSession()->set('_locale', $locale);

        return $this->redirect($request->headers->get('referer', $this->generateUrl('home.home')));
    }
}
