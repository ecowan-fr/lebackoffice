<?php

namespace App\Repository;

use Exception;
use App\Entity\Config;
use DateTimeImmutable;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Config|null find($id, $lockMode = null, $lockVersion = null)
 * @method Config|null findOneBy(array $criteria, array $orderBy = null)
 * @method Config[]    findAll()
 * @method Config[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfigRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Config::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Config $entity, bool $flush = true): void {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Config $entity, bool $flush = true): void {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getAll(): array {
        $configs = $this->findAll();
        $data = [];
        foreach ($configs as $key => $value) {
            $data[$value->getSetting()] = $value->getValue();
        }

        return $data;
    }

    public function saveMultiple(array $data) {
        foreach ($data as $setting => $value) {
            try {
                $this->save($setting, $value);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    public function save(string $setting, string $value) {
        try {
            /** @var Config */
            $Config = $this->findOneBy(['setting' => $setting]);
            if (!is_null($Config)) {
                $Config
                    ->setValue($value)
                    ->setUpdatedAt(new DateTimeImmutable);
                $this->_em->flush();
                return true;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getLogos(array $configs): array {
        $logos = [];
        if (!$configs['structure_logo_custom']) {
            $logos['light'] = "/images/logo/logo-lebackoffice-noir.svg";
            $logos['dark'] = "/images/logo/logo-lebackoffice-blanc.svg";
            $logos['icone'] = "/images/logo/icone-lebackoffice.svg";
        } else {
            $logos['light'] = $configs['structure_logo_url_light'];
            $logos['dark'] = $configs['structure_logo_url_dark'];
            $logos['icone'] = $configs['structure_icone_url'];
        }

        return $logos;
    }
}
