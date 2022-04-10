<?php

namespace App\Repository;

use App\Entity\PublicKeyCredentialMetadata;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PublicKeyCredentialMetadata|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublicKeyCredentialMetadata|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublicKeyCredentialMetadata[]    findAll()
 * @method PublicKeyCredentialMetadata[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicKeyCredentialMetadataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicKeyCredentialMetadata::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PublicKeyCredentialMetadata $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(PublicKeyCredentialMetadata $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return PublicKeyCredentialMetadata[] Returns an array of PublicKeyCredentialMetadata objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PublicKeyCredentialMetadata
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
