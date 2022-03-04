<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController {

    #[Route('/{locale}', name: 'locale.change', requirements: ['locale' => 'fr|en'], methods: ['GET'])]
    public function fr(string $locale, Request $request): RedirectResponse {
        $request->getSession()->set('_locale', $locale);

        return $this->redirectToRoute('home.home');
    }
}
