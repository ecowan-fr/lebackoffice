<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220409122833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE public_key_credential_metadata (id INT AUTO_INCREMENT NOT NULL, aaguid VARCHAR(255) NOT NULL, metadata LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', status_report LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE web_authn_token_name');
        $this->addSql('ALTER TABLE public_key_credential_source ADD metadata_id INT NOT NULL, ADD name VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE public_key_credential_source ADD CONSTRAINT FK_6FFE5D44DC9EE959 FOREIGN KEY (metadata_id) REFERENCES public_key_credential_metadata (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6FFE5D44DC9EE959 ON public_key_credential_source (metadata_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE public_key_credential_source DROP FOREIGN KEY FK_6FFE5D44DC9EE959');
        $this->addSql('CREATE TABLE web_authn_token_name (id INT AUTO_INCREMENT NOT NULL, token_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_EA96E59541DEE7B9 (token_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE web_authn_token_name ADD CONSTRAINT FK_EA96E59541DEE7B9 FOREIGN KEY (token_id) REFERENCES public_key_credential_source (id)');
        $this->addSql('DROP TABLE public_key_credential_metadata');
        $this->addSql('DROP INDEX UNIQ_6FFE5D44DC9EE959 ON public_key_credential_source');
        $this->addSql('ALTER TABLE public_key_credential_source DROP metadata_id, DROP name, DROP created_at');
    }
}
