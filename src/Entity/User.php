<?php

namespace App\Entity;

use LogicException;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Scheb\TwoFactorBundle\Model\BackupCodeInterface;
use Scheb\TwoFactorBundle\Model\Totp\TotpConfiguration;
use Scheb\TwoFactorBundle\Model\TrustedDeviceInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Scheb\TwoFactorBundle\Model\Totp\TotpConfigurationInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface as TotpTwoFactorInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface as EmailTwoFactorInterface;

#[ORM\HasLifecycleCallbacks()]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
implements
    UserInterface,
    PasswordAuthenticatedUserInterface,
    EmailTwoFactorInterface,
    TrustedDeviceInterface,
    BackupCodeInterface,
    TotpTwoFactorInterface {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $email;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: true)]
    private $username;

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
    private $azureId;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $authCode;

    #[ORM\Column(type: 'boolean')]
    private $twofa_email = false;

    #[ORM\Column(type: 'integer')]
    private $trustedVersion = 0;

    #[ORM\Column(type: 'json')]
    private $backupCodes = [];

    #[ORM\Column(type: 'string', nullable: true)]
    private $totpSecret;

    #[ORM\Column(type: 'string', nullable: true)]
    private $totpAppName;

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
        $this->username = $email;

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

    public function setUsername(string $username): self {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getDisplayName(): string {
        return (string) $this->firstname . ' ' . $this->lastname;
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
            "login",

            "settings.view",
            "settings.main.view",
            "settings.main.edit",

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
            "settings.users.delete",

            "settings.service_mode.login",
            "settings.service_mode.edit"
        ]);

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function preCreate() {
        $this->setUpdatedAt(new DateTimeImmutable());
        if (is_null($this->getCreatedAt())) {
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

    public function getAzureId(): ?string {
        return $this->azureId;
    }

    public function setAzureId(?string $azureId): self {
        $this->azureId = $azureId;

        return $this;
    }

    public function useOauth(): bool {
        return null !== $this->discordId || null !== $this->googleId || null !== $this->githubId || null !== $this->microsoftId;
    }

    public function isEmailAuthEnabled(): bool {
        return $this->twofa_email;
    }

    public function getEmailAuthRecipient(): string {
        return $this->email;
    }

    public function getEmailAuthCode(): ?string {
        if (is_null($this->authCode)) {
            throw new LogicException('The email authentication code was not set');
        }

        return $this->authCode;
    }

    public function setEmailAuthCode(string $authCode): void {
        $this->authCode = $authCode;
    }

    public function getTwofa_email(): ?bool {
        return $this->twofa_email;
    }

    public function setTwofa_email(bool $twofa_email): self {
        $this->twofa_email = $twofa_email;

        return $this;
    }

    public function getTrustedTokenVersion(): int {
        return $this->trustedVersion;
    }

    public function setTrustedTokenVersion(int $trustedVersion): self {
        $this->trustedVersion = $trustedVersion;

        return $this;
    }

    public function hasTwofa(string $type): bool {
        switch ($type) {
            case 'email':
                return $this->getTwofa_email();
                break;
            case 'backupCode':
                return count($this->backupCodes) === 0 ? false : true;
        }
    }

    public function isBackupCode(string $code): bool {
        return in_array($code, $this->backupCodes);
    }

    public function invalidateBackupCode(string $code): void {
        $key = array_search($code, $this->backupCodes);
        if ($key !== false) {
            unset($this->backupCodes[$key]);
        }
    }

    public function addBackUpCode(string $backUpCode): void {
        if (!in_array($backUpCode, $this->backupCodes)) {
            $this->backupCodes[] = $backUpCode;
        }
    }

    public function clearBackupCode(): void {
        $this->backupCodes = [];
    }

    public function getbackupCodes(): array {
        return $this->backupCodes;
    }

    public function getTotpSecret(): string {
        return $this->totpSecret;
    }

    public function setTotpSecret(string $totpSecret): self {
        $this->totpSecret = $totpSecret;

        return $this;
    }

    public function getTotpAppName(): ?string {
        return $this->totpAppName;
    }

    public function setTotpAppName(string $totpAppName): self {
        $this->totpAppName = $totpAppName;

        return $this;
    }

    public function isTotpAuthenticationEnabled(): bool {
        return $this->totpSecret ? true : false;
    }

    public function getTotpAuthenticationUsername(): string {
        return $this->getUserIdentifier();
    }

    public function getTotpAuthenticationConfiguration(): TotpConfigurationInterface {
        return new TotpConfiguration($this->totpSecret, TotpConfiguration::ALGORITHM_SHA1, 20, 8);
    }
}
