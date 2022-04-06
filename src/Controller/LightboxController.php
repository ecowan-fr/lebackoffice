<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LightboxController extends AbstractController {

    public function welcome(): string {
        return $this->renderView('home/lightbox/welcome.html.twig');
    }
}
