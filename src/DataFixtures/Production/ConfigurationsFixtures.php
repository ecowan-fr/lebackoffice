<?php

namespace App\DataFixtures\Production;

use App\Entity\Config;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class ConfigurationsFixtures extends Fixture implements FixtureGroupInterface {

    public static function getGroups(): array {
        return ['production'];
    }

    public function load(ObjectManager $manager): void {

        $configurations = [
            ["name" => "login.password", "value" => "1"],
            ["name" => "login.oauth.discord", "value" => "0"],
            ["name" => "login.oauth.google", "value" => "0"],
            ["name" => "login.oauth.github", "value" => "0"],
            ["name" => "login.oauth.azure", "value" => "0"],
            ["name" => "structure_name", "value" => ""],
            ["name" => "structure_type", "value" => ""],
            ["name" => "structure_siret", "value" => ""],
            ["name" => "structure_siren", "value" => ""],
            ["name" => "structure_rna", "value" => ""],
            ["name" => "structure_vat", "value" => ""],
            ["name" => "structure_eori", "value" => ""],
            ["name" => "structure_ics", "value" => ""],
            ["name" => "structure_email", "value" => ""],
            ["name" => "structure_tel", "value" => ""],
            ["name" => "structure_adress", "value" => ""],
            ["name" => "custom_on_backoffice", "value" => "1"],
            ["name" => "structure_logo_url_light", "value" => "/assets/images/logo/vert-gris/logo-vert-fonce.svg"],
            ["name" => "structure_logo_url_dark", "value" => "/assets/images/logo/vert-blanc/logo-vert-clair.svg"]
        ];

        foreach ($configurations as $configData) {
            $Config = new Config;
            $Config
                ->setSetting($configData['name'])
                ->setValue($configData['value']);
            $manager->persist($Config);
        }

        $manager->flush();
    }
}
