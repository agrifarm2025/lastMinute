<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250226171146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, featured_text VARCHAR(100) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_23A0E66989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, quantite INT NOT NULL, prix INT NOT NULL, type_commande VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, adress VARCHAR(255) NOT NULL, paiment LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', date_creation_commande DATE NOT NULL, INDEX IDX_6EEAA67DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande_produit (commande_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_DF1E9E8782EA2E54 (commande_id), INDEX IDX_DF1E9E87F347EFB (produit_id), PRIMARY KEY(commande_id, produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, rate VARCHAR(255) NOT NULL, commentaire VARCHAR(255) NOT NULL, INDEX IDX_67F068BC7294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE costs (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE crop (id INT AUTO_INCREMENT NOT NULL, crop_event VARCHAR(30) NOT NULL, type_crop VARCHAR(30) NOT NULL, methode_crop VARCHAR(20) NOT NULL, date_plantation DATE NOT NULL, heure_crop TIME NOT NULL, date_crop DATE NOT NULL, heure_plantation TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE farm (id INT AUTO_INCREMENT NOT NULL, location VARCHAR(255) NOT NULL, name VARCHAR(20) NOT NULL, surface DOUBLE PRECISION NOT NULL, adress VARCHAR(255) NOT NULL, budget DOUBLE PRECISION NOT NULL, weather VARCHAR(20) DEFAULT NULL, description VARCHAR(255) NOT NULL, bir TINYINT(1) NOT NULL, photovoltaic TINYINT(1) NOT NULL, fence TINYINT(1) NOT NULL, irrigation TINYINT(1) NOT NULL, cabin TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE field (id INT AUTO_INCREMENT NOT NULL, farm_id INT DEFAULT NULL, crop_id INT DEFAULT NULL, surface DOUBLE PRECISION NOT NULL, name VARCHAR(20) NOT NULL, budget DOUBLE PRECISION NOT NULL, income DOUBLE PRECISION NOT NULL, outcome DOUBLE PRECISION NOT NULL, profit DOUBLE PRECISION NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_5BF5455865FCFA0D (farm_id), UNIQUE INDEX UNIQ_5BF54558888579EE (crop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, quantite INT NOT NULL, prix INT NOT NULL, categories VARCHAR(255) NOT NULL, date_creation_produit DATETIME DEFAULT NULL, date_modification_produit DATETIME DEFAULT NULL, approved TINYINT(1) NOT NULL, image_file_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE soildata (id INT AUTO_INCREMENT NOT NULL, crop_id INT DEFAULT NULL, niveau_ph DOUBLE PRECISION NOT NULL, humidite DOUBLE PRECISION NOT NULL, niveau_nutriment DOUBLE PRECISION NOT NULL, type_sol VARCHAR(30) NOT NULL, INDEX IDX_C58D9DB7888579EE (crop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, field_id INT DEFAULT NULL, name VARCHAR(20) NOT NULL, description VARCHAR(255) NOT NULL, status VARCHAR(10) NOT NULL, date DATE NOT NULL, ressource VARCHAR(255) NOT NULL, responsable VARCHAR(20) NOT NULL, priority VARCHAR(10) NOT NULL, estimated_duration VARCHAR(30) NOT NULL, deadline DATE NOT NULL, workers INT NOT NULL, last_updated DATE NOT NULL, payment_worker NUMERIC(10, 2) DEFAULT NULL, total DOUBLE PRECISION NOT NULL, INDEX IDX_527EDB25443707B0 (field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, last_name VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_verified TINYINT(1) NOT NULL, image_file_name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E8782EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E87F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF5455865FCFA0D FOREIGN KEY (farm_id) REFERENCES farm (id)');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF54558888579EE FOREIGN KEY (crop_id) REFERENCES crop (id)');
        $this->addSql('ALTER TABLE soildata ADD CONSTRAINT FK_C58D9DB7888579EE FOREIGN KEY (crop_id) REFERENCES crop (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25443707B0 FOREIGN KEY (field_id) REFERENCES field (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E8782EA2E54');
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E87F347EFB');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC7294869C');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF5455865FCFA0D');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF54558888579EE');
        $this->addSql('ALTER TABLE soildata DROP FOREIGN KEY FK_C58D9DB7888579EE');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25443707B0');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE commande_produit');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE costs');
        $this->addSql('DROP TABLE crop');
        $this->addSql('DROP TABLE farm');
        $this->addSql('DROP TABLE field');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE soildata');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
