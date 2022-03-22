<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220322130126 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE devis (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type_prestation_id INT NOT NULL, prix DOUBLE PRECISION NOT NULL, nb_heure_total DOUBLE PRECISION NOT NULL, INDEX IDX_8B27C52BA76ED395 (user_id), UNIQUE INDEX UNIQ_8B27C52BEEA87261 (type_prestation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE forfait (id INT AUTO_INCREMENT NOT NULL, garage_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_BBB5C482C4FFF555 (garage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE garage (id INT AUTO_INCREMENT NOT NULL, taux_horaire_id INT DEFAULT NULL, nom_garage VARCHAR(255) NOT NULL, emplacement VARCHAR(255) NOT NULL, rdv DATETIME NOT NULL, UNIQUE INDEX UNIQ_9F26610B9FB5EE6B (taux_horaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE liste_operation (id INT AUTO_INCREMENT NOT NULL, type_prestation_id INT NOT NULL, nom_operation VARCHAR(255) NOT NULL, temps_total TIME NOT NULL, prix_operation DOUBLE PRECISION NOT NULL, INDEX IDX_44F56034EEA87261 (type_prestation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pieces_necessaire (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, marque VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE taux_horaire (id INT AUTO_INCREMENT NOT NULL, liste_operation_id INT NOT NULL, t1 DOUBLE PRECISION NOT NULL, t2 DOUBLE PRECISION NOT NULL, t3 DOUBLE PRECISION NOT NULL, INDEX IDX_3C7A19AFED09E50C (liste_operation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_prestation (id INT AUTO_INCREMENT NOT NULL, nom_prestation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_prestation_pieces_necessaire (type_prestation_id INT NOT NULL, pieces_necessaire_id INT NOT NULL, INDEX IDX_13FBA59DEEA87261 (type_prestation_id), INDEX IDX_13FBA59D360DD7A1 (pieces_necessaire_id), PRIMARY KEY(type_prestation_id, pieces_necessaire_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vehicule (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, immat VARCHAR(255) DEFAULT NULL, marque VARCHAR(255) DEFAULT NULL, vinh VARCHAR(255) DEFAULT NULL, modele VARCHAR(255) DEFAULT NULL, version VARCHAR(255) DEFAULT NULL, INDEX IDX_292FFF1DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52BEEA87261 FOREIGN KEY (type_prestation_id) REFERENCES type_prestation (id)');
        $this->addSql('ALTER TABLE forfait ADD CONSTRAINT FK_BBB5C482C4FFF555 FOREIGN KEY (garage_id) REFERENCES garage (id)');
        $this->addSql('ALTER TABLE garage ADD CONSTRAINT FK_9F26610B9FB5EE6B FOREIGN KEY (taux_horaire_id) REFERENCES taux_horaire (id)');
        $this->addSql('ALTER TABLE liste_operation ADD CONSTRAINT FK_44F56034EEA87261 FOREIGN KEY (type_prestation_id) REFERENCES type_prestation (id)');
        $this->addSql('ALTER TABLE taux_horaire ADD CONSTRAINT FK_3C7A19AFED09E50C FOREIGN KEY (liste_operation_id) REFERENCES liste_operation (id)');
        $this->addSql('ALTER TABLE type_prestation_pieces_necessaire ADD CONSTRAINT FK_13FBA59DEEA87261 FOREIGN KEY (type_prestation_id) REFERENCES type_prestation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE type_prestation_pieces_necessaire ADD CONSTRAINT FK_13FBA59D360DD7A1 FOREIGN KEY (pieces_necessaire_id) REFERENCES pieces_necessaire (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reset_password_request DROP selector, DROP hashed_token, DROP requested_at, DROP expires_at');
        $this->addSql('ALTER TABLE user ADD nom VARCHAR(255) NOT NULL, ADD prenom VARCHAR(255) NOT NULL, ADD adresse VARCHAR(255) DEFAULT NULL, ADD gamme_produit VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forfait DROP FOREIGN KEY FK_BBB5C482C4FFF555');
        $this->addSql('ALTER TABLE taux_horaire DROP FOREIGN KEY FK_3C7A19AFED09E50C');
        $this->addSql('ALTER TABLE type_prestation_pieces_necessaire DROP FOREIGN KEY FK_13FBA59D360DD7A1');
        $this->addSql('ALTER TABLE garage DROP FOREIGN KEY FK_9F26610B9FB5EE6B');
        $this->addSql('ALTER TABLE devis DROP FOREIGN KEY FK_8B27C52BEEA87261');
        $this->addSql('ALTER TABLE liste_operation DROP FOREIGN KEY FK_44F56034EEA87261');
        $this->addSql('ALTER TABLE type_prestation_pieces_necessaire DROP FOREIGN KEY FK_13FBA59DEEA87261');
        $this->addSql('DROP TABLE devis');
        $this->addSql('DROP TABLE forfait');
        $this->addSql('DROP TABLE garage');
        $this->addSql('DROP TABLE liste_operation');
        $this->addSql('DROP TABLE pieces_necessaire');
        $this->addSql('DROP TABLE taux_horaire');
        $this->addSql('DROP TABLE type_prestation');
        $this->addSql('DROP TABLE type_prestation_pieces_necessaire');
        $this->addSql('DROP TABLE vehicule');
        $this->addSql('ALTER TABLE reset_password_request ADD selector VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD hashed_token VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE `user` DROP nom, DROP prenom, DROP adresse, DROP gamme_produit');
    }
}
