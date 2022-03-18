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
            ["name" => "structure.name", "value" => ""],
            ["name" => "structure.type", "value" => ""],
            ["name" => "structure.siret", "value" => ""],
            ["name" => "structure.siren", "value" => ""],
            ["name" => "structure.rna", "value" => ""],
            ["name" => "structure.vat", "value" => ""],
            ["name" => "structure.eori", "value" => ""],
            ["name" => "structure.ics", "value" => ""],
            ["name" => "structure.email", "value" => ""],
            ["name" => "structure.tel", "value" => ""],
            ["name" => "structure.adress", "value" => ""],
            ["name" => "structure.logo.custom", "value" => "1"],
            ["name" => "structure.logo.url.light", "value" => "/images/logo/logo-lebackoffice-noir.svg"],
            ["name" => "structure.logo.url.dark", "value" => "/images/logo/logo-lebackoffice-blanc.svg"]
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
