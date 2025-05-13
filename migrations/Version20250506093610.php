<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250506093610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        
        $this->addSql('ALTER TABLE farm ADD user_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE farm ADD CONSTRAINT FK_5816D0459D86650F FOREIGN KEY (user_id_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_5816D0459D86650F ON farm (user_id_id)');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF5455865FCFA0D FOREIGN KEY (farm_id) REFERENCES farm (id)');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF54558888579EE FOREIGN KEY (crop_id) REFERENCES crop (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
       
        $this->addSql('ALTER TABLE farm DROP FOREIGN KEY FK_5816D0459D86650F');
        $this->addSql('DROP INDEX IDX_5816D0459D86650F ON farm');
        $this->addSql('ALTER TABLE farm DROP user_id_id');
    }
}
