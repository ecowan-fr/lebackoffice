<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
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

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $createdAt;

    #[ORM\OneToOne(targetEntity: PublicKeyCredentialMetadata::class, cascade: ['persist', 'remove'], fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private $metadata;

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
        $this->createdAt = new DateTimeImmutable();
        parent::__construct($publicKeyCredentialId, $type, $transports, $attestationType, $trustPath, $aaguid, $credentialPublicKey, $userHandle, $counter);
    }

    public function getId(): ?string {
        return $this->id;
    }

    public function setId(?string $id): self {
        $this->id = $id;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getMetadata(): ?PublicKeyCredentialMetadata {
        return $this->metadata;
    }

    public function setMetadata(PublicKeyCredentialMetadata $metadata): self {
        $this->metadata = $metadata;

        return $this;
    }
}
