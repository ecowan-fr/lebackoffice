<?php

namespace App\DataProvider;

use Twig\Environment;
use App\Entity\Lightbox;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LightboxDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface {

    public function __construct(
        private readonly string $twig_path,
        private readonly Environment $twig
    ) {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool {
        return $resourceClass === Lightbox::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []) {
        $lightbox = 'lightbox/' . $id . '.html.twig';
        return file_exists($this->twig_path . '/' . $lightbox) ? new Lightbox(name: $id, html: $this->twig->render($lightbox)) : null;
    }
}
