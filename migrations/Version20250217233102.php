<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217233102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cropgrowth (id INT AUTO_INCREMENT NOT NULL, stages LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', observations VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stages (id INT AUTO_INCREMENT NOT NULL, cropgrowth_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, startdate DATE NOT NULL, enddate DATE NOT NULL, INDEX IDX_2FA26A649737F62B (cropgrowth_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stages ADD CONSTRAINT FK_2FA26A649737F62B FOREIGN KEY (cropgrowth_id) REFERENCES cropgrowth (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stages DROP FOREIGN KEY FK_2FA26A649737F62B');
        $this->addSql('DROP TABLE cropgrowth');
        $this->addSql('DROP TABLE stages');
    }
}
