<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250305005254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE crop DROP zone_id, DROP cities');
        $this->addSql('ALTER TABLE farm ADD lon DOUBLE PRECISION NOT NULL, ADD lat DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE soildata ADD CONSTRAINT FK_C58D9DB7888579EE FOREIGN KEY (crop_id) REFERENCES crop (id)');
        $this->addSql('ALTER TABLE task CHANGE last_updated last_updated DATE NOT NULL, CHANGE payment_worker payment_worker NUMERIC(10, 2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE crop ADD zone_id INT NOT NULL, ADD cities VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE farm DROP lon, DROP lat');
        $this->addSql('ALTER TABLE soildata DROP FOREIGN KEY FK_C58D9DB7888579EE');
        $this->addSql('ALTER TABLE task CHANGE last_updated last_updated DATE DEFAULT NULL, CHANGE payment_worker payment_worker DOUBLE PRECISION NOT NULL');
    }
}
