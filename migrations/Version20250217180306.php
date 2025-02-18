<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217180306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stages ADD cropgrowth_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stages ADD CONSTRAINT FK_2FA26A649737F62B FOREIGN KEY (cropgrowth_id) REFERENCES cropgrowth (id)');
        $this->addSql('CREATE INDEX IDX_2FA26A649737F62B ON stages (cropgrowth_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stages DROP FOREIGN KEY FK_2FA26A649737F62B');
        $this->addSql('DROP INDEX IDX_2FA26A649737F62B ON stages');
        $this->addSql('ALTER TABLE stages DROP cropgrowth_id');
    }
}
