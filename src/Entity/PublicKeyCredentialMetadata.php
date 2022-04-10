<?php

namespace App\Entity;

use App\Repository\PublicKeyCredentialMetadataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicKeyCredentialMetadataRepository::class)]
class PublicKeyCredentialMetadata
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $aaguid;

    #[ORM\Column(type: 'json', nullable: true)]
    private $metadata = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private $statusReport = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAaguid(): ?string
    {
        return $this->aaguid;
    }

    public function setAaguid(string $aaguid): self
    {
        $this->aaguid = $aaguid;

        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function getStatusReport(): ?array
    {
        return $this->statusReport;
    }

    public function setStatusReport(?array $statusReport): self
    {
        $this->statusReport = $statusReport;

        return $this;
    }
}
