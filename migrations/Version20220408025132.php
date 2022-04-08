<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220408025132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE public_key_credential_source (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', public_key_credential_id LONGTEXT NOT NULL COMMENT \'(DC2Type:base64)\', type VARCHAR(255) NOT NULL, transports LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', attestation_type VARCHAR(255) NOT NULL, trust_path LONGTEXT NOT NULL COMMENT \'(DC2Type:trust_path)\', aaguid TINYTEXT NOT NULL COMMENT \'(DC2Type:aaguid)\', credential_public_key LONGTEXT NOT NULL COMMENT \'(DC2Type:base64)\', user_handle VARCHAR(255) NOT NULL, counter INT NOT NULL, other_ui LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE public_key_credential_source');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('ALTER TABLE user DROP username');
    }
}
