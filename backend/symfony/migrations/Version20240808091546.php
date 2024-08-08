<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240808091546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE storage (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, max_storage BIGINT NOT NULL, used_storage BIGINT NOT NULL, account_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_547A1B349B6B5FBA ON storage (account_id)');
        $this->addSql('ALTER TABLE storage ADD CONSTRAINT FK_547A1B349B6B5FBA FOREIGN KEY (account_id) REFERENCES "account" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('INSERT INTO settings (id, key, value) VALUES (2, \'video_size_limit\', \'500MB\')');
        $this->addSql('INSERT INTO settings (id, key, value) VALUES (3, \'default_user_storage_limit\', \'5GB\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE storage DROP CONSTRAINT FK_547A1B349B6B5FBA');
        $this->addSql('DROP TABLE storage');
        $this->addSql('DELETE FROM settings WHERE key = \'video_size_limit\'');
        $this->addSql('DELETE FROM settings WHERE key = \'default_user_storage_limit\'');
    }
}
