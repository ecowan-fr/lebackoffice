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

    public function saveMultiple(array $data): void {
        foreach ($data as $setting => $value) {
            try {
                $this->save($setting, $value);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    public function save(string $setting, string $value): bool {
        /** @var Config */
        $Config = $this->findOneBy(['setting' => $setting]);
        if (!is_null($Config)) {
            $Config
                ->setValue($value)
                ->setUpdatedAt(new DateTimeImmutable);
            $this->_em->flush();
            return true;
        }

        return throw new Exception("Impossible d'enregistrer la configuration.");
    }

    public function getLogos(array $configs): array {
        $logos = [];
        if (!$configs['structure.logo.custom']) {
            $logos['light'] = "/images/logo/logo-lebackoffice-noir.svg";
            $logos['dark'] = "/images/logo/logo-lebackoffice-blanc.svg";
        } else {
            $logos['light'] = $configs['structure.logo.url.light'];
            $logos['dark'] = $configs['structure.logo.url.dark'];
        }

        return $logos;
    }

    // /**
    //  * @return Config[] Returns an array of Config objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Config
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
