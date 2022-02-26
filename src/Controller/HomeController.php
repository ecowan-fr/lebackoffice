<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {
    #[Route('/', name: 'home.index')]
    public function index(): RedirectResponse {
        return $this->redirectToRoute('home.home');
    }

    #[Route('/home', name: 'home.home')]
    public function home(): Response {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
