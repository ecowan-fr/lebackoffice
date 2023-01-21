<?php

namespace App\State;

use App\Entity\Lightbox;
use ApiPlatform\Metadata\Operation;
use App\Controller\LightboxController;
use ApiPlatform\State\ProviderInterface;

class LightboxProvider implements ProviderInterface {

    public function __construct(
        private readonly CheckServiceMode $checkServiceModeProvider,
        private readonly LightboxController $lightboxController
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null {
        $js = "<script type='text/javascript'>document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (event) => {
                var btn = form.querySelector('button[type=\"submit\"]')
                btn.innerHTML = '<i class=\'fad animate-spin fa-spinner-third\'></i>'
                btn.disabled = true
            })
        })</script>";

        return method_exists(LightboxController::class, $uriVariables['name']) ? new Lightbox(name: $uriVariables['name'], html: $this->lightboxController->{$uriVariables['name']}() . $js) : null;
    }
}
