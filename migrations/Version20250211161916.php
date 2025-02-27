<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250211161916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit ADD description LONGTEXT NOT NULL, ADD image_file_name VARCHAR(255) DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, DROP descriiption, DROP image_file, CHANGE prix prix NUMERIC(10, 2) NOT NULL, CHANGE date_creation_produit date_creation_produit DATETIME NOT NULL, CHANGE date_modification_produit date_modification_produit DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit ADD descriiption VARCHAR(255) NOT NULL, ADD image_file VARCHAR(255) NOT NULL, DROP description, DROP image_file_name, DROP updated_at, CHANGE prix prix INT NOT NULL, CHANGE date_creation_produit date_creation_produit DATE NOT NULL, CHANGE date_modification_produit date_modification_produit DATE NOT NULL');
    }
}
