<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240711111233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE md5_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE video_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE md5 (id INT NOT NULL, md5 VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E86CEBE1E86CEBE1 ON md5 (md5)');
        $this->addSql('CREATE TABLE video (id INT NOT NULL, account_id INT NOT NULL, md5_id INT NOT NULL, folder_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, hash VARCHAR(255) NOT NULL, extension VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, size BIGINT NOT NULL, length BIGINT NOT NULL, cdn_id INT NOT NULL, cdn_link VARCHAR(255) NOT NULL, original_width INT NOT NULL, original_height INT NOT NULL, thumbs_count INT NOT NULL, is_deleted BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7CC7DA2CD1B862B8 ON video (hash)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7CC7DA2C11E991C8 ON video (cdn_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7CC7DA2CDBEA601A ON video (cdn_link)');
        $this->addSql('CREATE INDEX IDX_7CC7DA2C9B6B5FBA ON video (account_id)');
        $this->addSql('CREATE INDEX IDX_7CC7DA2C37EF781B ON video (md5_id)');
        $this->addSql('CREATE INDEX IDX_7CC7DA2C162CB942 ON video (folder_id)');
        $this->addSql('COMMENT ON COLUMN video.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN video.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C9B6B5FBA FOREIGN KEY (account_id) REFERENCES "account" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C37EF781B FOREIGN KEY (md5_id) REFERENCES md5 (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C162CB942 FOREIGN KEY (folder_id) REFERENCES folder (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE md5_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE video_id_seq CASCADE');
        $this->addSql('ALTER TABLE video DROP CONSTRAINT FK_7CC7DA2C9B6B5FBA');
        $this->addSql('ALTER TABLE video DROP CONSTRAINT FK_7CC7DA2C37EF781B');
        $this->addSql('ALTER TABLE video DROP CONSTRAINT FK_7CC7DA2C162CB942');
        $this->addSql('DROP TABLE md5');
        $this->addSql('DROP TABLE video');
    }
}
