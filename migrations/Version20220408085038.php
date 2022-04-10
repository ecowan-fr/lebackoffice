<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220408085038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE web_authn_token_name (id INT AUTO_INCREMENT NOT NULL, token_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_EA96E59541DEE7B9 (token_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE web_authn_token_name ADD CONSTRAINT FK_EA96E59541DEE7B9 FOREIGN KEY (token_id) REFERENCES public_key_credential_source (id)');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE web_authn_token_name');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(255) NOT NULL');
    }
}
