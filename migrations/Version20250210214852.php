<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210214852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE crop DROP niveau_ph, DROP humidite, DROP niveau_nutriment, DROP type_sol');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE crop ADD niveau_ph DOUBLE PRECISION NOT NULL, ADD humidite DOUBLE PRECISION NOT NULL, ADD niveau_nutriment DOUBLE PRECISION NOT NULL, ADD type_sol VARCHAR(20) NOT NULL');
    }
}
