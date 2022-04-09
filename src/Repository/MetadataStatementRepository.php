<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\JWTService;
use App\Entity\PublicKeyCredentialMetadata;
use App\Repository\PublicKeyCredentialMetadataRepository;
use Webauthn\MetadataService\Statement\MetadataStatement;
use Webauthn\MetadataService\MetadataStatementRepository as MetadataStatementRepositoryInterface;

final class MetadataStatementRepository implements MetadataStatementRepositoryInterface {

    public function __construct(
        private JWTService $jWTService,
        private PublicKeyCredentialMetadataRepository $publicKeyCredentialMetadataRepository
    ) {
    }

    public function findOneByAAGUID(string $aaguid): ?MetadataStatement {
        $metadata = $this->jWTService->getMetadataStatement($aaguid);
        $this->publicKeyCredentialMetadataRepository->add(
            (new PublicKeyCredentialMetadata())
                ->setAaguid($aaguid)
                ->setMetadata($metadata->jsonSerialize())
        );

        return $metadata;
    }
}
