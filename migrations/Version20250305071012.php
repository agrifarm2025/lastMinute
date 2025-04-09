<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250305071012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_article (category_id INT NOT NULL, article_id INT NOT NULL, INDEX IDX_C5E24E1812469DE2 (category_id), INDEX IDX_C5E24E187294869C (article_id), PRIMARY KEY(category_id, article_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_article ADD CONSTRAINT FK_C5E24E1812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_article ADD CONSTRAINT FK_C5E24E187294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande ADD user_id INT DEFAULT NULL, CHANGE paiment paiment VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DA76ED395 ON commande (user_id)');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E87F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('DROP INDEX UNIQ_EDC23D9B640ED2C0 ON crop');
        $this->addSql('ALTER TABLE crop DROP income_id');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF5455865FCFA0D FOREIGN KEY (farm_id) REFERENCES farm (id)');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF54558888579EE FOREIGN KEY (crop_id) REFERENCES crop (id)');
        $this->addSql('ALTER TABLE produit ADD description LONGTEXT NOT NULL, ADD image_file_name VARCHAR(255) DEFAULT NULL, DROP descriiption, DROP image_file, CHANGE date_creation_produit date_creation_produit DATETIME DEFAULT NULL, CHANGE date_modification_produit date_modification_produit DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE soildata CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE soildata ADD CONSTRAINT FK_C58D9DB7888579EE FOREIGN KEY (crop_id) REFERENCES crop (id)');
        $this->addSql('ALTER TABLE task CHANGE last_updated last_updated DATE NOT NULL, CHANGE payment_worker payment_worker NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25443707B0 FOREIGN KEY (field_id) REFERENCES field (id)');
        $this->addSql('ALTER TABLE users ADD reset_token VARCHAR(255) DEFAULT NULL, ADD google_id VARCHAR(255) DEFAULT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE last_name last_name VARCHAR(255) NOT NULL, CHANGE first_name first_name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_article DROP FOREIGN KEY FK_C5E24E1812469DE2');
        $this->addSql('ALTER TABLE category_article DROP FOREIGN KEY FK_C5E24E187294869C');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_article');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('DROP INDEX IDX_6EEAA67DA76ED395 ON commande');
        $this->addSql('ALTER TABLE commande DROP user_id, CHANGE paiment paiment LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E87F347EFB');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC7294869C');
        $this->addSql('ALTER TABLE crop ADD income_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EDC23D9B640ED2C0 ON crop (income_id)');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF5455865FCFA0D');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF54558888579EE');
        $this->addSql('ALTER TABLE produit ADD descriiption VARCHAR(255) NOT NULL, ADD image_file VARCHAR(255) NOT NULL, DROP description, DROP image_file_name, CHANGE date_creation_produit date_creation_produit DATE NOT NULL, CHANGE date_modification_produit date_modification_produit DATE NOT NULL');
        $this->addSql('ALTER TABLE soildata DROP FOREIGN KEY FK_C58D9DB7888579EE');
        $this->addSql('ALTER TABLE soildata CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25443707B0');
        $this->addSql('ALTER TABLE task CHANGE last_updated last_updated DATE DEFAULT NULL, CHANGE payment_worker payment_worker DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE users DROP reset_token, DROP google_id, CHANGE id id INT NOT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL');
    }
}
