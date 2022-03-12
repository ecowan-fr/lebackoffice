<?php

namespace App\Services;

use App\Entity\Config;
use App\Repository\ConfigRepository;

class Configuration {

    public function __construct(
        private ConfigRepository $configRepository
    ) {
    }

    public function get(string $setting): ?string {
        $data = $this->configRepository->findOneBy(['setting' => $setting]);
        return $data ? $data->getSetting() : null;
    }
}
