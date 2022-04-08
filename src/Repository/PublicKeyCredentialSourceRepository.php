<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PublicKeyCredentialSource;
use Doctrine\Persistence\ManagerRegistry;
use Webauthn\PublicKeyCredentialSource as BasePublicKeyCredentialSource;
use Webauthn\Bundle\Repository\PublicKeyCredentialSourceRepository as BasePublicKeyCredentialSourceRepository;

final class PublicKeyCredentialSourceRepository extends BasePublicKeyCredentialSourceRepository {
    public function __construct(ManagerRegistry $registry) {
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
        }
        parent::saveCredentialSource($publicKeyCredentialSource, $flush);
    }
}
