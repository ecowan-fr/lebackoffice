<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PublicKeyCredentialSource;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Repository\PublicKeyCredentialMetadataRepository;
use Webauthn\PublicKeyCredentialSource as BasePublicKeyCredentialSource;
use Webauthn\Bundle\Repository\PublicKeyCredentialSourceRepository as BasePublicKeyCredentialSourceRepository;

final class PublicKeyCredentialSourceRepository extends BasePublicKeyCredentialSourceRepository {

    private Session $session;

    public function __construct(
        ManagerRegistry $registry,
        RequestStack $requestStack,
        private PublicKeyCredentialMetadataRepository $publicKeyCredentialMetadataRepository,
        private TranslatorInterface $translator
    ) {
        $this->session = $requestStack->getSession();
        parent::__construct($registry, PublicKeyCredentialSource::class);
    }

    public function saveCredentialSource(BasePublicKeyCredentialSource $publicKeyCredentialSource, bool $flush = true): void {
        if (!$publicKeyCredentialSource instanceof PublicKeyCredentialSource) {
            $publicKeyCredentialSource = new PublicKeyCredentialSource(
                $publicKeyCredentialSource->getPublicKeyCredentialId(),
                $publicKeyCredentialSource->getType(),
                $publicKeyCredentialSource->getTransports(),
                $publicKeyCredentialSource->getAttestationType(),
                $publicKeyCredentialSource->getTrustPath(),
                $publicKeyCredentialSource->getAaguid(),
                $publicKeyCredentialSource->getCredentialPublicKey(),
                $publicKeyCredentialSource->getUserHandle(),
                $publicKeyCredentialSource->getCounter()
            );

            $metadata = $this->publicKeyCredentialMetadataRepository->findOneBy(['aaguid' => $publicKeyCredentialSource->getAaguid()]);
            $publicKeyCredentialSource->setMetadata($metadata);
        }

        parent::saveCredentialSource($publicKeyCredentialSource, $flush);
    }

    public function findOneByAaguid(string $aaguid): ?PublicKeyCredentialSource {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('c')
            ->from($this->getClass(), 'c')
            ->where('c.aaguid = :aaguid')
            ->setParameter(':aaguid', $aaguid)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function removeByAaguid(string $aaguid): void {
        $entity = $this->findOneByAaguid($aaguid);
        $em = $this->getEntityManager();
        $em->remove($entity);
        $em->flush();
    }
}
