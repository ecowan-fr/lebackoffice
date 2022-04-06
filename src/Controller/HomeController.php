<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {
    #[Route(path: '/', name: 'home.index', methods: ['GET'])]
    public function index(): RedirectResponse {
        return $this->redirectToRoute('home.home');
    }

    #[Route(path: '/home', name: 'home.home', methods: ['GET'])]
    public function home(): Response {
        return $this->render('home/index.html.twig');
    }
}
