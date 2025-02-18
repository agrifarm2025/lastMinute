<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217171452 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE culture ADD croissanceculture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE culture ADD CONSTRAINT FK_B6A99CEBA860F990 FOREIGN KEY (croissanceculture_id) REFERENCES croissance_culture (id)');
        $this->addSql('CREATE INDEX IDX_B6A99CEBA860F990 ON culture (croissanceculture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE culture DROP FOREIGN KEY FK_B6A99CEBA860F990');
        $this->addSql('DROP INDEX IDX_B6A99CEBA860F990 ON culture');
        $this->addSql('ALTER TABLE culture DROP croissanceculture_id');
    }
}
