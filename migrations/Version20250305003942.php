<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250305003942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE costs (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E8782EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E87F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('DROP INDEX UNIQ_EDC23D9B640ED2C0 ON crop');
        $this->addSql('ALTER TABLE crop DROP income_id');
        $this->addSql('ALTER TABLE farm ADD lon DOUBLE PRECISION NOT NULL, ADD lat DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE field CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF5455865FCFA0D FOREIGN KEY (farm_id) REFERENCES farm (id)');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF54558888579EE FOREIGN KEY (crop_id) REFERENCES crop (id)');
        $this->addSql('ALTER TABLE produit ADD description LONGTEXT NOT NULL, ADD image_file_name VARCHAR(255) DEFAULT NULL, DROP descriiption, DROP image_file, CHANGE date_creation_produit date_creation_produit DATETIME DEFAULT NULL, CHANGE date_modification_produit date_modification_produit DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE soildata CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE soildata ADD CONSTRAINT FK_C58D9DB7888579EE FOREIGN KEY (crop_id) REFERENCES crop (id)');
        $this->addSql('ALTER TABLE task CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE last_updated last_updated DATE NOT NULL, CHANGE payment_worker payment_worker NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25443707B0 FOREIGN KEY (field_id) REFERENCES field (id)');
        $this->addSql('ALTER TABLE users CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }   

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE costs');
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E8782EA2E54');
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E87F347EFB');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC7294869C');
        $this->addSql('ALTER TABLE crop ADD income_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EDC23D9B640ED2C0 ON crop (income_id)');
        $this->addSql('ALTER TABLE farm DROP lon, DROP lat');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF5455865FCFA0D');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF54558888579EE');
        $this->addSql('ALTER TABLE field CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE produit ADD descriiption VARCHAR(255) NOT NULL, ADD image_file VARCHAR(255) NOT NULL, DROP description, DROP image_file_name, CHANGE date_creation_produit date_creation_produit DATE NOT NULL, CHANGE date_modification_produit date_modification_produit DATE NOT NULL');
        $this->addSql('ALTER TABLE soildata DROP FOREIGN KEY FK_C58D9DB7888579EE');
        $this->addSql('ALTER TABLE soildata CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25443707B0');
        $this->addSql('ALTER TABLE task CHANGE id id INT NOT NULL, CHANGE last_updated last_updated DATE DEFAULT NULL, CHANGE payment_worker payment_worker DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE id id INT NOT NULL');
    }
}
