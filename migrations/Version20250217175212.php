<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217175212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cropgrowth DROP plantation, DROP croissance, DROP maturite, DROP recolte');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cropgrowth ADD plantation VARCHAR(30) NOT NULL, ADD croissance VARCHAR(30) NOT NULL, ADD maturite VARCHAR(30) NOT NULL, ADD recolte VARCHAR(30) NOT NULL');
    }
}
