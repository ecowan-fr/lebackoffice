<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\HasLifecycleCallbacks()]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 255)]
    private $lastname;

    #[ORM\Column(type: 'string', length: 255)]
    private $firstname;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $updatedAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $emailPerso;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $lastLoginIp;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $lastLoginAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $discordId;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $googleId;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $githubId;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $microsoftId;

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): self {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials() {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastname(): ?string {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self {
        $this->firstname = $firstname;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getEmailPerso(): ?string {
        return $this->emailPerso;
    }

    public function setEmailPerso(?string $emailPerso): self {
        $this->emailPerso = $emailPerso;

        return $this;
    }

    public function setFullRoles(): self {

        $this->setRoles([
            "settings.view",
            "settings.main.view",

            "settings.services.vmware.view",
            "settings.services.vmware.edit",
            "settings.services.vmware.delete",

            "settings.services.pterodactyl.view",
            "settings.services.pterodactyl.edit",
            "settings.services.pterodactyl.delete",

            "settings.services.plesk.view",
            "settings.services.plesk.edit",
            "settings.services.plesk.delete",

            "settings.services.domains.view",
            "settings.services.domains.edit",
            "settings.services.domains.delete",

            "settings.services.licences.view",
            "settings.services.licences.edit",
            "settings.services.licences.delete",

            "settings.payments.view",
            "settings.payments.edit",
            "settings.payments.delete",

            "settings.billings.view",
            "settings.billings.edit",
            "settings.billings.delete",

            "settings.security.view",
            "settings.security.edit",
            "settings.security.delete",

            "settings.users.view",
            "settings.users.edit",
            "settings.users.delete"
        ]);

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updatedTimestamps(): void {
        $this->setUpdatedAt(new DateTimeImmutable());
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new DateTimeImmutable());
        }
    }

    public function getLastLoginIp(): ?string {
        return $this->lastLoginIp;
    }

    public function setLastLoginIp(string $lastLoginIp): self {
        $this->lastLoginIp = $lastLoginIp;

        return $this;
    }

    public function getLastLoginAt(): ?DateTimeImmutable {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(DateTimeImmutable $lastLoginAt): self {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function getDiscordId(): ?string {
        return $this->discordId;
    }

    public function setDiscordId(?string $discordId): self {
        $this->discordId = $discordId;

        return $this;
    }

    public function getGoogleId(): ?string {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): self {
        $this->googleId = $googleId;

        return $this;
    }

    public function getGithubId(): ?string {
        return $this->githubId;
    }

    public function setGithubId(?string $githubId): self {
        $this->githubId = $githubId;

        return $this;
    }

    public function getMicrosoftId(): ?string {
        return $this->microsoftId;
    }

    public function setMicrosoftId(?string $microsoftId): self {
        $this->microsoftId = $microsoftId;

        return $this;
    }

    public function useOauth(): bool {
        return null !== $this->discordId || null !== $this->googleId || null !== $this->githubId || null !== $this->microsoftId;
    }
}
