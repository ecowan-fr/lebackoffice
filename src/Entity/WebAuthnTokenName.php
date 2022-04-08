<?php

namespace App\Entity;

use App\Repository\WebAuthnTokenNameRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WebAuthnTokenNameRepository::class)]
class WebAuthnTokenName {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\OneToOne(inversedBy: 'webAuthnTokenName', targetEntity: PublicKeyCredentialSource::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $token;

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getToken(): ?PublicKeyCredentialSource {
        return $this->token;
    }

    public function setToken(PublicKeyCredentialSource $token): self {
        $this->token = $token;

        return $this;
    }
}
