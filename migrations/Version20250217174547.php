<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217174547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE crop ADD cropgrowth_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE crop ADD CONSTRAINT FK_EDC23D9B9737F62B FOREIGN KEY (cropgrowth_id) REFERENCES cropgrowth (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EDC23D9B9737F62B ON crop (cropgrowth_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE crop DROP FOREIGN KEY FK_EDC23D9B9737F62B');
        $this->addSql('DROP INDEX UNIQ_EDC23D9B9737F62B ON crop');
        $this->addSql('ALTER TABLE crop DROP cropgrowth_id');
    }
}
