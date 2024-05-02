<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240502145631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avisLivraison (idAvis INT AUTO_INCREMENT NOT NULL, commentaire VARCHAR(200) NOT NULL, idLivraison INT NOT NULL, INDEX IDX_E375FE795AE6B449 (idLivraison), PRIMARY KEY(idAvis)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE forum (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, created_at DATE NOT NULL, username VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE livraison (idLivraison INT AUTO_INCREMENT NOT NULL, dateLivraison DATE NOT NULL, statut VARCHAR(50) DEFAULT \'en attente\' NOT NULL, idPartenaire INT DEFAULT NULL, idCommande INT NOT NULL, INDEX IDX_A60C9F1F3D498C26 (idCommande), PRIMARY KEY(idLivraison)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avisLivraison ADD CONSTRAINT FK_E375FE795AE6B449 FOREIGN KEY (idLivraison) REFERENCES livraison (idLivraison)');
        $this->addSql('ALTER TABLE livraison ADD CONSTRAINT FK_A60C9F1F3D498C26 FOREIGN KEY (idCommande) REFERENCES commands (id)');
        $this->addSql('ALTER TABLE achats MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON achats');
        $this->addSql('ALTER TABLE achats CHANGE id idAchats INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE achats ADD PRIMARY KEY (idAchats)');
        $this->addSql('ALTER TABLE basket CHANGE remise remise VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE command_articles DROP FOREIGN KEY FK_8631E8F933E1689A');
        $this->addSql('ALTER TABLE command_articles DROP FOREIGN KEY FK_8631E8F97294869C');
        $this->addSql('ALTER TABLE command_articles ADD CONSTRAINT FK_8631E8F933E1689A FOREIGN KEY (command_id) REFERENCES commands (id)');
        $this->addSql('ALTER TABLE command_articles ADD CONSTRAINT FK_8631E8F97294869C FOREIGN KEY (article_id) REFERENCES produits (idProduit)');
        $this->addSql('ALTER TABLE commands DROP FOREIGN KEY FK_9A3E132CE173B1B8');
        $this->addSql('ALTER TABLE commands ADD CONSTRAINT FK_9A3E132CE173B1B8 FOREIGN KEY (id_client) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C727ACA70');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C727ACA70 FOREIGN KEY (parent_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FED8EF5D7');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FED8EF5D7 FOREIGN KEY (idProduits) REFERENCES produits (idProduit)');
        $this->addSql('ALTER TABLE partenaires CHANGE logo logo VARCHAR(200) DEFAULT NULL');
        $this->addSql('ALTER TABLE produits CHANGE average_rating average_rating DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation_enchere ADD dateReservation DATE DEFAULT NULL, ADD confirmation TINYINT(1) DEFAULT NULL, ADD idUser INT DEFAULT NULL, ADD idEnchere INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation_enchere ADD CONSTRAINT FK_5F443C8AFE6E88D7 FOREIGN KEY (idUser) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE reservation_enchere ADD CONSTRAINT FK_5F443C8A2868ECFD FOREIGN KEY (idEnchere) REFERENCES encheres (idEnchere)');
        $this->addSql('CREATE INDEX IDX_5F443C8AFE6E88D7 ON reservation_enchere (idUser)');
        $this->addSql('CREATE INDEX IDX_5F443C8A2868ECFD ON reservation_enchere (idEnchere)');
        $this->addSql('ALTER TABLE service_apres_vente CHANGE description description VARCHAR(255) NOT NULL, CHANGE type type VARCHAR(255) NOT NULL, CHANGE idAchats idAchats INT NOT NULL');
        $this->addSql('ALTER TABLE service_apres_vente ADD CONSTRAINT FK_E8A0B369FA313AD4 FOREIGN KEY (idAchats) REFERENCES achats (idAchats)');
        $this->addSql('ALTER TABLE service_apres_vente ADD CONSTRAINT FK_E8A0B369B00BBD99 FOREIGN KEY (idPartenaire) REFERENCES partenaires (idPartenaire)');
        $this->addSql('CREATE INDEX IDX_E8A0B369FA313AD4 ON service_apres_vente (idAchats)');
        $this->addSql('CREATE INDEX IDX_E8A0B369B00BBD99 ON service_apres_vente (idPartenaire)');
        $this->addSql('ALTER TABLE views DROP FOREIGN KEY FK_11F09C87F347EFB');
        $this->addSql('ALTER TABLE views DROP FOREIGN KEY FK_11F09C87FB88E14F');
        $this->addSql('ALTER TABLE views ADD CONSTRAINT FK_11F09C87F347EFB FOREIGN KEY (produit_id) REFERENCES produits (idProduit)');
        $this->addSql('ALTER TABLE views ADD CONSTRAINT FK_11F09C87FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avisLivraison DROP FOREIGN KEY FK_E375FE795AE6B449');
        $this->addSql('ALTER TABLE livraison DROP FOREIGN KEY FK_A60C9F1F3D498C26');
        $this->addSql('DROP TABLE avisLivraison');
        $this->addSql('DROP TABLE forum');
        $this->addSql('DROP TABLE livraison');
        $this->addSql('ALTER TABLE achats MODIFY idAchats INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON achats');
        $this->addSql('ALTER TABLE achats CHANGE idAchats id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE achats ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE basket CHANGE remise remise VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE commands DROP FOREIGN KEY FK_9A3E132CE173B1B8');
        $this->addSql('ALTER TABLE commands ADD CONSTRAINT FK_9A3E132CE173B1B8 FOREIGN KEY (id_client) REFERENCES utilisateur (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE command_articles DROP FOREIGN KEY FK_8631E8F97294869C');
        $this->addSql('ALTER TABLE command_articles DROP FOREIGN KEY FK_8631E8F933E1689A');
        $this->addSql('ALTER TABLE command_articles ADD CONSTRAINT FK_8631E8F97294869C FOREIGN KEY (article_id) REFERENCES produits (idProduit) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE command_articles ADD CONSTRAINT FK_8631E8F933E1689A FOREIGN KEY (command_id) REFERENCES commands (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C727ACA70');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C727ACA70 FOREIGN KEY (parent_id) REFERENCES comment (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FED8EF5D7');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FED8EF5D7 FOREIGN KEY (idProduits) REFERENCES produits (idProduit) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE partenaires CHANGE logo logo VARCHAR(200) NOT NULL');
        $this->addSql('ALTER TABLE produits CHANGE average_rating average_rating DOUBLE PRECISION DEFAULT \'0\'');
        $this->addSql('ALTER TABLE reservation_enchere DROP FOREIGN KEY FK_5F443C8AFE6E88D7');
        $this->addSql('ALTER TABLE reservation_enchere DROP FOREIGN KEY FK_5F443C8A2868ECFD');
        $this->addSql('DROP INDEX IDX_5F443C8AFE6E88D7 ON reservation_enchere');
        $this->addSql('DROP INDEX IDX_5F443C8A2868ECFD ON reservation_enchere');
        $this->addSql('ALTER TABLE reservation_enchere DROP dateReservation, DROP confirmation, DROP idUser, DROP idEnchere');
        $this->addSql('ALTER TABLE service_apres_vente DROP FOREIGN KEY FK_E8A0B369FA313AD4');
        $this->addSql('ALTER TABLE service_apres_vente DROP FOREIGN KEY FK_E8A0B369B00BBD99');
        $this->addSql('DROP INDEX IDX_E8A0B369FA313AD4 ON service_apres_vente');
        $this->addSql('DROP INDEX IDX_E8A0B369B00BBD99 ON service_apres_vente');
        $this->addSql('ALTER TABLE service_apres_vente CHANGE description description VARCHAR(200) NOT NULL, CHANGE type type VARCHAR(50) NOT NULL, CHANGE idAchats idAchats INT DEFAULT NULL');
        $this->addSql('ALTER TABLE views DROP FOREIGN KEY FK_11F09C87FB88E14F');
        $this->addSql('ALTER TABLE views DROP FOREIGN KEY FK_11F09C87F347EFB');
        $this->addSql('ALTER TABLE views ADD CONSTRAINT FK_11F09C87FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE views ADD CONSTRAINT FK_11F09C87F347EFB FOREIGN KEY (produit_id) REFERENCES produits (idProduit) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
