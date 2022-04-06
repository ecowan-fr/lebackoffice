<?php

namespace App\DataFixtures\Production;

use DateTimeImmutable;
use App\Entity\Config;
use App\Repository\ConfigRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class ConfigurationsFixtures extends Fixture implements FixtureGroupInterface {

    public function __construct(
        private readonly ConfigRepository $configRepository
    ) {
    }

    public static function getGroups(): array {
        return ['production'];
    }

    public function load(ObjectManager $manager): void {

        $configurations = [
            ["name" => "login_password", "value" => "1"],
            ["name" => "login_oauth_discord", "value" => "0"],
            ["name" => "login_oauth_google", "value" => "0"],
            ["name" => "login_oauth_github", "value" => "0"],
            ["name" => "login_oauth_azure", "value" => "0"],

            ["name" => "structure_name", "value" => ""],
            ["name" => "structure_type", "value" => ""],
            ["name" => "structure_rcs", "value" => ""],
            ["name" => "structure_siret", "value" => ""],
            ["name" => "structure_siren", "value" => ""],
            ["name" => "structure_rna", "value" => ""],
            ["name" => "structure_vat", "value" => ""],
            ["name" => "structure_eori", "value" => ""],
            ["name" => "structure_ics", "value" => ""],
            ["name" => "structure_email", "value" => ""],
            ["name" => "structure_tel", "value" => ""],
            ["name" => "structure_adress", "value" => ""],
            ["name" => "structure_logo_custom", "value" => "1"],
            ["name" => "structure_logo_url_light", "value" => "/images/logo/logo-lebackoffice-noir.svg"],
            ["name" => "structure_logo_url_dark", "value" => "/images/logo/logo-lebackoffice-blanc.svg"],

            ["name" => "footer_active", "value" => "1"],
            ["name" => "footer_left_type", "value" => "logo"], //logo || text || timbre || null
            ["name" => "footer_left_text", "value" => null],
            ["name" => "footer_center_type", "value" => null], //logo || text || timbre || null
            ["name" => "footer_center_text", "value" => null],
            ["name" => "footer_right_type", "value" => "text"], //logo || text || timbre || null
            ["name" => "footer_right_text", "value" => "Confidentiel"]
        ];

        foreach ($configurations as $configData) {
            if (is_null($this->configRepository->findOneBy(['setting' => $configData['name']]))) {
                $Config = new Config;
                $Config
                    ->setSetting($configData['name'])
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setUpdatedAt(new DateTimeImmutable());

                if (!is_null($configData['value'])) {
                    $Config->setValue($configData['value']);
                }

                $manager->persist($Config);
            }
        }

        $manager->flush();
    }
}
