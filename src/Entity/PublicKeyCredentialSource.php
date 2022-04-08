<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Uid\Ulid;
use Doctrine\ORM\Mapping as ORM;
use Webauthn\TrustPath\TrustPath;
use Symfony\Component\Uid\AbstractUid;
use App\Repository\PublicKeyCredentialSourceRepository;
use Webauthn\PublicKeyCredentialSource as BasePublicKeyCredentialSource;

#[ORM\Entity(repositoryClass: PublicKeyCredentialSourceRepository::class)]
class PublicKeyCredentialSource extends BasePublicKeyCredentialSource {
    #[ORM\Id]
    #[ORM\Column(type: "ulid", unique: true)]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private string $id;

    public function __construct(
        string $publicKeyCredentialId,
        string $type,
        array $transports,
        string $attestationType,
        TrustPath $trustPath,
        AbstractUid $aaguid,
        string $credentialPublicKey,
        string $userHandle,
        int $counter
    ) {
        $this->id = Ulid::generate();
        parent::__construct($publicKeyCredentialId, $type, $transports, $attestationType, $trustPath, $aaguid, $credentialPublicKey, $userHandle, $counter);
    }

    public function getId(): ?string {
        return $this->id;
    }

    public function setId(?string $id): self {
        $this->id = $id;

        return $this;
    }
}
