<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\JWTService;
use App\Repository\PublicKeyCredentialMetadataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Webauthn\MetadataService\StatusReportRepository as StatusReportRepositoryInterface;

final class StatusReportRepository implements StatusReportRepositoryInterface {
    public function __construct(
        private JWTService $jWTService,
        private PublicKeyCredentialMetadataRepository $publicKeyCredentialMetadataRepository,
        private EntityManagerInterface $em
    ) {
    }

    public function findStatusReportsByAAGUID(string $aaguid): array {
        $statusReports = $this->jWTService->getStatusReports($aaguid);
        $metadata = $this->publicKeyCredentialMetadataRepository->findOneBy([
            "aaguid" => $aaguid
        ]);

        if (!is_null($metadata)) {
            $metadata->setStatusReport($statusReports);
            $this->em->flush();
        }

        return $this->jWTService->getStatusReports($aaguid);
    }
}
