<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218221822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE crop ADD income_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE crop ADD CONSTRAINT FK_EDC23D9B640ED2C0 FOREIGN KEY (income_id) REFERENCES income (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EDC23D9B640ED2C0 ON crop (income_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE crop DROP FOREIGN KEY FK_EDC23D9B640ED2C0');
        $this->addSql('DROP INDEX UNIQ_EDC23D9B640ED2C0 ON crop');
        $this->addSql('ALTER TABLE crop DROP income_id');
    }
}
