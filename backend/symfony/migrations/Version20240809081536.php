<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240809081536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE share_folder (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, folder_id INT NOT NULL, account_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3290F9E4162CB942 ON share_folder (folder_id)');
        $this->addSql('CREATE INDEX IDX_3290F9E49B6B5FBA ON share_folder (account_id)');
        $this->addSql('CREATE TABLE share_video (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, video_id INT NOT NULL, account_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F5A936C129C1004E ON share_video (video_id)');
        $this->addSql('CREATE INDEX IDX_F5A936C19B6B5FBA ON share_video (account_id)');
        $this->addSql('CREATE TABLE share_video_public (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, hash VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expired_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, video_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B641A26329C1004E ON share_video_public (video_id)');
        $this->addSql('CREATE TABLE share_video_public_view (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, session_id VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, share_video_public_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_59EFE94FB13A1F92 ON share_video_public_view (share_video_public_id)');
        $this->addSql('ALTER TABLE share_folder ADD CONSTRAINT FK_3290F9E4162CB942 FOREIGN KEY (folder_id) REFERENCES folder (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE share_folder ADD CONSTRAINT FK_3290F9E49B6B5FBA FOREIGN KEY (account_id) REFERENCES "account" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE share_video ADD CONSTRAINT FK_F5A936C129C1004E FOREIGN KEY (video_id) REFERENCES video (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE share_video ADD CONSTRAINT FK_F5A936C19B6B5FBA FOREIGN KEY (account_id) REFERENCES "account" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE share_video_public ADD CONSTRAINT FK_B641A26329C1004E FOREIGN KEY (video_id) REFERENCES video (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE share_video_public_view ADD CONSTRAINT FK_59EFE94FB13A1F92 FOREIGN KEY (share_video_public_id) REFERENCES share_video_public (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('INSERT INTO settings (id, key, value) VALUES (4, \'public_link_limit\', \'3\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE share_folder DROP CONSTRAINT FK_3290F9E4162CB942');
        $this->addSql('ALTER TABLE share_folder DROP CONSTRAINT FK_3290F9E49B6B5FBA');
        $this->addSql('ALTER TABLE share_video DROP CONSTRAINT FK_F5A936C129C1004E');
        $this->addSql('ALTER TABLE share_video DROP CONSTRAINT FK_F5A936C19B6B5FBA');
        $this->addSql('ALTER TABLE share_video_public DROP CONSTRAINT FK_B641A26329C1004E');
        $this->addSql('ALTER TABLE share_video_public_view DROP CONSTRAINT FK_59EFE94FB13A1F92');
        $this->addSql('DROP TABLE share_folder');
        $this->addSql('DROP TABLE share_video');
        $this->addSql('DROP TABLE share_video_public');
        $this->addSql('DROP TABLE share_video_public_view');
        $this->addSql('DELETE FROM settings WHERE key = \'public_link_limit\'');
    }
}
