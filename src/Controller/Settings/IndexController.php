<?php

namespace App\Controller\Settings;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
}
