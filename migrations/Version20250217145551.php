<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217145551 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE farm ADD adress VARCHAR(255) NOT NULL, ADD budget DOUBLE PRECISION NOT NULL, ADD weather VARCHAR(20) DEFAULT NULL, ADD description VARCHAR(255) NOT NULL, ADD created_at DATE NOT NULL, ADD bir TINYINT(1) NOT NULL, ADD photovoltaic TINYINT(1) NOT NULL, ADD fence TINYINT(1) NOT NULL, ADD irrigation TINYINT(1) NOT NULL, ADD cabin TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE field ADD budget DOUBLE PRECISION NOT NULL, ADD income DOUBLE PRECISION NOT NULL, ADD outcome DOUBLE PRECISION NOT NULL, ADD profit DOUBLE PRECISION NOT NULL, ADD description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE task ADD priority VARCHAR(10) NOT NULL, ADD estimated_duration VARCHAR(30) NOT NULL, ADD deadline DATE NOT NULL, ADD workers INT NOT NULL, ADD last_updated DATE NOT NULL, ADD payment_worker DOUBLE PRECISION NOT NULL, ADD total DOUBLE PRECISION NOT NULL, CHANGE status status VARCHAR(10) NOT NULL');
        $this->addSql("ALTER TABLE farm MODIFY created_at DATETIME DEFAULT NULL");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE farm DROP adress, DROP budget, DROP weather, DROP description, DROP created_at, DROP bir, DROP photovoltaic, DROP fence, DROP irrigation, DROP cabin');
        $this->addSql('ALTER TABLE field DROP budget, DROP income, DROP outcome, DROP profit, DROP description');
        $this->addSql('ALTER TABLE task DROP priority, DROP estimated_duration, DROP deadline, DROP workers, DROP last_updated, DROP payment_worker, DROP total, CHANGE status status VARCHAR(20) NOT NULL');
    }
}
