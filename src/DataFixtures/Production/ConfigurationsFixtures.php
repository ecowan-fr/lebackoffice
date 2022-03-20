<?php

namespace App\DataFixtures\Production;

use App\Entity\Config;
use App\Repository\ConfigRepository;
use DateTimeImmutable;
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
            ["name" => "structure.logo.url.dark", "value" => "/images/logo/logo-lebackoffice-blanc.svg"],

            ["name" => "footer.active", "value" => "1"],
            ["name" => "footer.left.type", "value" => "logo"], //logo || text || timbre || null
            ["name" => "footer.left.text", "value" => null],
            ["name" => "footer.center.type", "value" => null], //logo || text || timbre || null
            ["name" => "footer.center.text", "value" => null],
            ["name" => "footer.right.type", "value" => "text"], //logo || text || timbre || null
            ["name" => "footer.right.text", "value" => "Confidentiel"]
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
