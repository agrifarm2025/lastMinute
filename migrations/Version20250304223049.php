<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250304223049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande CHANGE paiment paiment VARCHAR(255) NOT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DA76ED395 ON commande (user_id)');
        $this->addSql('ALTER TABLE soildata ADD temperature DOUBLE PRECISION DEFAULT NULL, ADD humidity DOUBLE PRECISION DEFAULT NULL, CHANGE niveau_ph niveau_ph DOUBLE PRECISION DEFAULT NULL, CHANGE humidite humidite DOUBLE PRECISION DEFAULT NULL, CHANGE niveau_nutriment niveau_nutriment DOUBLE PRECISION DEFAULT NULL, CHANGE type_sol type_sol VARCHAR(30) DEFAULT NULL, CHANGE crop_id crop_id INT NOT NULL');
        $this->addSql('ALTER TABLE soildata ADD CONSTRAINT FK_C58D9DB7888579EE FOREIGN KEY (crop_id) REFERENCES crop (id)');
        $this->addSql('ALTER TABLE task CHANGE last_updated last_updated DATE NOT NULL, CHANGE payment_worker payment_worker NUMERIC(10, 2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('DROP INDEX IDX_6EEAA67DA76ED395 ON commande');
        $this->addSql('ALTER TABLE commande CHANGE user_id user_id INT NOT NULL, CHANGE paiment paiment LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE soildata DROP FOREIGN KEY FK_C58D9DB7888579EE');
        $this->addSql('ALTER TABLE soildata DROP temperature, DROP humidity, CHANGE crop_id crop_id INT DEFAULT NULL, CHANGE niveau_ph niveau_ph DOUBLE PRECISION NOT NULL, CHANGE humidite humidite DOUBLE PRECISION NOT NULL, CHANGE niveau_nutriment niveau_nutriment DOUBLE PRECISION NOT NULL, CHANGE type_sol type_sol VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE task CHANGE last_updated last_updated DATE DEFAULT NULL, CHANGE payment_worker payment_worker DOUBLE PRECISION NOT NULL');
    }
}
