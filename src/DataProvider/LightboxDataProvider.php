<?php

namespace App\DataProvider;

use App\Entity\Lightbox;
use App\Controller\LightboxController;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;

class LightboxDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface {

    public function __construct(
        private LightboxController $lightboxController
    ) {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool {
        return $resourceClass === Lightbox::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []) {
        $js = "<script type='text/javascript'>document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (event) => {
                var btn = form.querySelector('button[type=\"submit\"]')
                btn.innerHTML = '<i class=\'fad animate-spin fa-spinner-third\'></i>'
                btn.disabled = true
            })
        })</script>";
        return method_exists(LightboxController::class, $id) ? new Lightbox(name: $id, html: $this->lightboxController->{$id}() . $js) : null;
    }
}
