<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Repository\ConfigRepository;
use Exception;

class CheckServiceModeDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface {

    public function __construct(private readonly ConfigRepository $configRepository) {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool {
        return $this->configRepository->findOneBy(['setting' => 'service_mode'])->getValue();
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []) {
        throw new Exception('Service mode is enabled');
    }
}
