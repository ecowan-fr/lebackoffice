<?php

namespace App\DataProvider;

use App\Entity\Lightbox;
use App\Controller\LightboxController;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;

class LightboxDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface {

    public function __construct(
        private readonly string $twig_path,
        private LightboxController $lightboxController
    ) {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool {
        return $resourceClass === Lightbox::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []) {
        return method_exists(LightboxController::class, $id) ? new Lightbox(name: $id, html: $this->lightboxController->{$id}()) : null;
    }
}
