<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219021251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_EDC23D9B640ED2C0 ON crop');
        $this->addSql('ALTER TABLE crop DROP income_id');
        $this->addSql('ALTER TABLE field ADD crop_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF54558888579EE FOREIGN KEY (crop_id) REFERENCES crop (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5BF54558888579EE ON field (crop_id)');
        $this->addSql('ALTER TABLE soildata ADD CONSTRAINT FK_C58D9DB7888579EE FOREIGN KEY (crop_id) REFERENCES crop (id)');
        $this->addSql('ALTER TABLE task CHANGE last_updated last_updated DATE NOT NULL, CHANGE payment_worker payment_worker NUMERIC(10, 2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE crop ADD income_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EDC23D9B640ED2C0 ON crop (income_id)');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF54558888579EE');
        $this->addSql('DROP INDEX UNIQ_5BF54558888579EE ON field');
        $this->addSql('ALTER TABLE field DROP crop_id');
        $this->addSql('ALTER TABLE soildata DROP FOREIGN KEY FK_C58D9DB7888579EE');
        $this->addSql('ALTER TABLE task CHANGE last_updated last_updated DATE DEFAULT NULL, CHANGE payment_worker payment_worker DOUBLE PRECISION NOT NULL');
    }
}
