<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240817080352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account DROP is_deleted');
        $this->addSql('ALTER TABLE folder ALTER normalized_name DROP DEFAULT');
        $this->addSql('ALTER TABLE video DROP is_deleted');
        $this->addSql('ALTER TABLE video DROP deleted_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE video ADD is_deleted BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE video ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE "account" ADD is_deleted BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE folder ALTER normalized_name SET DEFAULT \'\'');
    }
}
