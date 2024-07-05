<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240705132012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "account_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE api_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE folder_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "account" (id INT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, salt VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_active BOOLEAN NOT NULL, is_deleted BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "account".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE account_role (account_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY(account_id, role_id))');
        $this->addSql('CREATE INDEX IDX_DBFA0DC09B6B5FBA ON account_role (account_id)');
        $this->addSql('CREATE INDEX IDX_DBFA0DC0D60322AC ON account_role (role_id)');
        $this->addSql('CREATE TABLE api_token (id INT NOT NULL, account_id INT NOT NULL, refresh_token TEXT NOT NULL, session_id VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7BA2F5EBC74F2195 ON api_token (refresh_token)');
        $this->addSql('CREATE INDEX IDX_7BA2F5EB9B6B5FBA ON api_token (account_id)');
        $this->addSql('COMMENT ON COLUMN api_token.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN api_token.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE folder (id INT NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ECA209CD7E3C61F9 ON folder (owner_id)');
        $this->addSql('COMMENT ON COLUMN folder.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN folder.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE role (id INT NOT NULL, name VARCHAR(255) NOT NULL, key_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE account_role ADD CONSTRAINT FK_DBFA0DC09B6B5FBA FOREIGN KEY (account_id) REFERENCES "account" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE account_role ADD CONSTRAINT FK_DBFA0DC0D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EB9B6B5FBA FOREIGN KEY (account_id) REFERENCES "account" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE folder ADD CONSTRAINT FK_ECA209CD7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "account" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('INSERT INTO role (id, name, key_name) VALUES (1, \'Uživatel\', \'ROLE_USER\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "account_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE api_token_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE folder_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE role_id_seq CASCADE');
        $this->addSql('ALTER TABLE account_role DROP CONSTRAINT FK_DBFA0DC09B6B5FBA');
        $this->addSql('ALTER TABLE account_role DROP CONSTRAINT FK_DBFA0DC0D60322AC');
        $this->addSql('ALTER TABLE api_token DROP CONSTRAINT FK_7BA2F5EB9B6B5FBA');
        $this->addSql('ALTER TABLE folder DROP CONSTRAINT FK_ECA209CD7E3C61F9');
        $this->addSql('DROP TABLE "account"');
        $this->addSql('DROP TABLE account_role');
        $this->addSql('DROP TABLE api_token');
        $this->addSql('DROP TABLE folder');
        $this->addSql('DROP TABLE role');
    }
}
