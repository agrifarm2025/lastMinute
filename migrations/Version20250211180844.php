<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250211180844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit CHANGE prix prix INT NOT NULL, CHANGE date_creation_produit date_creation_produit DATETIME DEFAULT NULL, CHANGE date_modification_produit date_modification_produit DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit CHANGE prix prix NUMERIC(10, 2) NOT NULL, CHANGE date_creation_produit date_creation_produit DATETIME NOT NULL, CHANGE date_modification_produit date_modification_produit DATETIME NOT NULL');
    }
}
