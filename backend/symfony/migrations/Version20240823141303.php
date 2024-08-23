<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240823141303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_76518edc613fecdf');
        $this->addSql('ALTER TABLE storage_card_payment RENAME COLUMN session_id TO payment_intent');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_76518EDC9B546087 ON storage_card_payment (payment_intent)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_76518EDC9B546087');
        $this->addSql('ALTER TABLE storage_card_payment RENAME COLUMN payment_intent TO session_id');
        $this->addSql('CREATE UNIQUE INDEX uniq_76518edc613fecdf ON storage_card_payment (session_id)');
    }
}
