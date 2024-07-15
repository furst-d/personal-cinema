<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240715133716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE video ALTER type DROP NOT NULL');
        $this->addSql('ALTER TABLE video ALTER length DROP NOT NULL');
        $this->addSql('ALTER TABLE video ALTER original_width DROP NOT NULL');
        $this->addSql('ALTER TABLE video ALTER original_height DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE video ALTER type SET NOT NULL');
        $this->addSql('ALTER TABLE video ALTER length SET NOT NULL');
        $this->addSql('ALTER TABLE video ALTER original_width SET NOT NULL');
        $this->addSql('ALTER TABLE video ALTER original_height SET NOT NULL');
    }
}
