<?php

namespace App\State;

use Exception;
use App\Repository\ConfigRepository;

class CheckServiceMode {
    public function __construct(private ConfigRepository $configRepository) {
        if ($this->configRepository->findOneBy(['setting' => 'service_mode'])->getValue()) {
            throw new Exception('Service mode is enabled');
        }
    }
}
