<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217232430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE crop DROP FOREIGN KEY FK_EDC23D9B9737F62B');
        $this->addSql('ALTER TABLE stages DROP FOREIGN KEY FK_2FA26A649737F62B');
        $this->addSql('DROP TABLE cropgrowth');
        $this->addSql('DROP TABLE stages');
        $this->addSql('DROP INDEX UNIQ_EDC23D9B9737F62B ON crop');
        $this->addSql('ALTER TABLE crop DROP cropgrowth_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cropgrowth (id INT AUTO_INCREMENT NOT NULL, datedebut DATE NOT NULL, datefin DATE NOT NULL, expectations VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, observation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, stages VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE stages (id INT AUTO_INCREMENT NOT NULL, cropgrowth_id INT DEFAULT NULL, plantation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, croissance VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, maturite VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, recolte VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_2FA26A649737F62B (cropgrowth_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE stages ADD CONSTRAINT FK_2FA26A649737F62B FOREIGN KEY (cropgrowth_id) REFERENCES cropgrowth (id)');
        $this->addSql('ALTER TABLE crop ADD cropgrowth_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE crop ADD CONSTRAINT FK_EDC23D9B9737F62B FOREIGN KEY (cropgrowth_id) REFERENCES cropgrowth (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EDC23D9B9737F62B ON crop (cropgrowth_id)');
    }
}
