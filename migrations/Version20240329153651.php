<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240329153651 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE encheres DROP FOREIGN KEY fk_enchere_produit');
        $this->addSql('ALTER TABLE reservation_enchere DROP FOREIGN KEY fk_reservation_enchere_utilisateur');
        $this->addSql('ALTER TABLE reservation_enchere DROP FOREIGN KEY fk_reservation_enchere_enchere');
        $this->addSql('ALTER TABLE reservation_location DROP FOREIGN KEY fk_reservation_location_location');
        $this->addSql('ALTER TABLE reservation_location DROP FOREIGN KEY fk_reservation_location_utilisateur');
        $this->addSql('DROP TABLE encheres');
        $this->addSql('DROP TABLE reservation_enchere');
        $this->addSql('DROP TABLE reservation_location');
        $this->addSql('ALTER TABLE achats MODIFY idAchats INT NOT NULL');
        $this->addSql('ALTER TABLE achats DROP FOREIGN KEY fk_id_produi');
        $this->addSql('ALTER TABLE achats DROP FOREIGN KEY fk_id_user');
        $this->addSql('DROP INDEX fk_id_user ON achats');
        $this->addSql('DROP INDEX fk_id_produi ON achats');
        $this->addSql('DROP INDEX `primary` ON achats');
        $this->addSql('ALTER TABLE achats DROP idProduits, DROP idUtilisateur, DROP dateAchats, CHANGE idAchats id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE achats ADD PRIMARY KEY (id)');
        $this->addSql('DROP INDEX UNIQ_2246507BF7384557 ON basket');
        $this->addSql('DROP INDEX UNIQ_2246507BE173B1B8 ON basket');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY fk_id_location');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY fk_id_prod');
        $this->addSql('DROP INDEX fk_id_prod ON image');
        $this->addSql('CREATE INDEX IDX_C53D045FED8EF5D7 ON image (idProduits)');
        $this->addSql('DROP INDEX fk_id_location ON image');
        $this->addSql('CREATE INDEX IDX_C53D045FEB0C61EC ON image (idLocations)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT fk_id_location FOREIGN KEY (idLocations) REFERENCES location (idLocation)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT fk_id_prod FOREIGN KEY (idProduits) REFERENCES produits (idProduit)');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY fk_location_user');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY fk_location_user');
        $this->addSql('ALTER TABLE location CHANGE prix prix DOUBLE PRECISION NOT NULL, CHANGE type type VARCHAR(300) NOT NULL, CHANGE description description VARCHAR(300) NOT NULL, CHANGE adresse adresse VARCHAR(300) NOT NULL, CHANGE disponibilite disponibilite VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CBBF396750 FOREIGN KEY (id) REFERENCES utilisateur (id)');
        $this->addSql('DROP INDEX fk_location_user ON location');
        $this->addSql('CREATE INDEX IDX_5E9E89CBBF396750 ON location (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT fk_location_user FOREIGN KEY (id) REFERENCES utilisateur (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE partenaires DROP status, CHANGE points points INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY fk_produit_user');
        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY fk_produit_user');
        $this->addSql('ALTER TABLE produits CHANGE idUtilisateur idUtilisateur INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT FK_BE2DDF8C5D419CCB FOREIGN KEY (idUtilisateur) REFERENCES utilisateur (id)');
        $this->addSql('DROP INDEX fk_user_prod ON produits');
        $this->addSql('CREATE INDEX IDX_BE2DDF8C5D419CCB ON produits (idUtilisateur)');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT fk_produit_user FOREIGN KEY (idUtilisateur) REFERENCES utilisateur (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ratings DROP FOREIGN KEY fk_produit');
        $this->addSql('ALTER TABLE ratings DROP FOREIGN KEY fk_user');
        $this->addSql('ALTER TABLE ratings CHANGE id_user id_user INT DEFAULT NULL, CHANGE id_produit id_produit INT DEFAULT NULL');
        $this->addSql('DROP INDEX fk_produit ON ratings');
        $this->addSql('CREATE INDEX IDX_CEB607C9F7384557 ON ratings (id_produit)');
        $this->addSql('DROP INDEX fk_user ON ratings');
        $this->addSql('CREATE INDEX IDX_CEB607C96B3CA4B ON ratings (id_user)');
        $this->addSql('ALTER TABLE ratings ADD CONSTRAINT fk_produit FOREIGN KEY (id_produit) REFERENCES produits (idProduit)');
        $this->addSql('ALTER TABLE ratings ADD CONSTRAINT fk_user FOREIGN KEY (id_user) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE service_apres_vente DROP FOREIGN KEY fk_id_achats');
        $this->addSql('ALTER TABLE service_apres_vente DROP FOREIGN KEY fk_service_partenaire');
        $this->addSql('DROP INDEX idPartenaire ON service_apres_vente');
        $this->addSql('DROP INDEX fk_id_achats ON service_apres_vente');
        $this->addSql('ALTER TABLE service_apres_vente CHANGE status status TINYINT(1) NOT NULL, CHANGE idAchats idAchats INT DEFAULT NULL');
        $this->addSql('DROP INDEX adresseEmail ON utilisateur');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE encheres (idEnchere INT AUTO_INCREMENT NOT NULL, dateDebut DATE DEFAULT NULL, dateFin DATE DEFAULT NULL, Status TINYINT(1) DEFAULT NULL, prixMin DOUBLE PRECISION DEFAULT NULL, prixMax DOUBLE PRECISION DEFAULT NULL, prixActuelle DOUBLE PRECISION DEFAULT NULL, nbrParticipants INT NOT NULL, idProduit INT NOT NULL, INDEX fk_enchere_produit (idProduit), PRIMARY KEY(idEnchere)) DEFAULT CHARACTER SET latin1 COLLATE `latin1_swedish_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reservation_enchere (idReservation INT AUTO_INCREMENT NOT NULL, idEnchere INT DEFAULT NULL, idUser INT DEFAULT NULL, dateReservation DATE DEFAULT NULL, confirmation TINYINT(1) DEFAULT NULL, INDEX idUser (idUser), INDEX idEnchere (idEnchere), PRIMARY KEY(idReservation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reservation_location (id_reservation_location INT AUTO_INCREMENT NOT NULL, dateDebut DATE DEFAULT NULL, dateFin DATE DEFAULT NULL, idUtilisateur INT DEFAULT NULL, idLocation INT DEFAULT NULL, notes TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, INDEX idLocation (idLocation), INDEX idUtilisateur (idUtilisateur), PRIMARY KEY(id_reservation_location)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE encheres ADD CONSTRAINT fk_enchere_produit FOREIGN KEY (idProduit) REFERENCES produits (idProduit) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation_enchere ADD CONSTRAINT fk_reservation_enchere_utilisateur FOREIGN KEY (idUser) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE reservation_enchere ADD CONSTRAINT fk_reservation_enchere_enchere FOREIGN KEY (idEnchere) REFERENCES encheres (idEnchere)');
        $this->addSql('ALTER TABLE reservation_location ADD CONSTRAINT fk_reservation_location_location FOREIGN KEY (idLocation) REFERENCES location (idLocation)');
        $this->addSql('ALTER TABLE reservation_location ADD CONSTRAINT fk_reservation_location_utilisateur FOREIGN KEY (idUtilisateur) REFERENCES utilisateur (id)');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE achats MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON achats');
        $this->addSql('ALTER TABLE achats ADD idProduits INT DEFAULT NULL, ADD idUtilisateur INT NOT NULL, ADD dateAchats DATE NOT NULL, CHANGE id idAchats INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE achats ADD CONSTRAINT fk_id_produi FOREIGN KEY (idProduits) REFERENCES produits (idProduit)');
        $this->addSql('ALTER TABLE achats ADD CONSTRAINT fk_id_user FOREIGN KEY (idUtilisateur) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX fk_id_user ON achats (idUtilisateur)');
        $this->addSql('CREATE INDEX fk_id_produi ON achats (idProduits)');
        $this->addSql('ALTER TABLE achats ADD PRIMARY KEY (idAchats)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2246507BF7384557 ON basket (id_produit)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2246507BE173B1B8 ON basket (id_client)');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FED8EF5D7');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FEB0C61EC');
        $this->addSql('DROP INDEX idx_c53d045fed8ef5d7 ON image');
        $this->addSql('CREATE INDEX fk_id_prod ON image (idProduits)');
        $this->addSql('DROP INDEX idx_c53d045feb0c61ec ON image');
        $this->addSql('CREATE INDEX fk_id_location ON image (idLocations)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FED8EF5D7 FOREIGN KEY (idProduits) REFERENCES produits (idProduit)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FEB0C61EC FOREIGN KEY (idLocations) REFERENCES location (idLocation)');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CBBF396750');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CBBF396750');
        $this->addSql('ALTER TABLE location CHANGE type type VARCHAR(50) NOT NULL, CHANGE description description TEXT NOT NULL, CHANGE prix prix NUMERIC(10, 2) NOT NULL, CHANGE adresse adresse VARCHAR(255) NOT NULL, CHANGE disponibilite disponibilite TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT fk_location_user FOREIGN KEY (id) REFERENCES utilisateur (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('DROP INDEX idx_5e9e89cbbf396750 ON location');
        $this->addSql('CREATE INDEX fk_location_user ON location (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CBBF396750 FOREIGN KEY (id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE partenaires ADD status INT DEFAULT 0, CHANGE points points INT DEFAULT 0');
        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY FK_BE2DDF8C5D419CCB');
        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY FK_BE2DDF8C5D419CCB');
        $this->addSql('ALTER TABLE produits CHANGE idUtilisateur idUtilisateur INT NOT NULL');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT fk_produit_user FOREIGN KEY (idUtilisateur) REFERENCES utilisateur (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('DROP INDEX idx_be2ddf8c5d419ccb ON produits');
        $this->addSql('CREATE INDEX fk_user_prod ON produits (idUtilisateur)');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT FK_BE2DDF8C5D419CCB FOREIGN KEY (idUtilisateur) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE ratings DROP FOREIGN KEY FK_CEB607C9F7384557');
        $this->addSql('ALTER TABLE ratings DROP FOREIGN KEY FK_CEB607C96B3CA4B');
        $this->addSql('ALTER TABLE ratings CHANGE id_produit id_produit INT NOT NULL, CHANGE id_user id_user INT NOT NULL');
        $this->addSql('DROP INDEX idx_ceb607c9f7384557 ON ratings');
        $this->addSql('CREATE INDEX fk_produit ON ratings (id_produit)');
        $this->addSql('DROP INDEX idx_ceb607c96b3ca4b ON ratings');
        $this->addSql('CREATE INDEX fk_user ON ratings (id_user)');
        $this->addSql('ALTER TABLE ratings ADD CONSTRAINT FK_CEB607C9F7384557 FOREIGN KEY (id_produit) REFERENCES produits (idProduit)');
        $this->addSql('ALTER TABLE ratings ADD CONSTRAINT FK_CEB607C96B3CA4B FOREIGN KEY (id_user) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE service_apres_vente CHANGE status status TINYINT(1) DEFAULT 0 NOT NULL, CHANGE idAchats idAchats INT NOT NULL');
        $this->addSql('ALTER TABLE service_apres_vente ADD CONSTRAINT fk_id_achats FOREIGN KEY (idAchats) REFERENCES achats (idAchats)');
        $this->addSql('ALTER TABLE service_apres_vente ADD CONSTRAINT fk_service_partenaire FOREIGN KEY (idPartenaire) REFERENCES partenaires (idPartenaire) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('CREATE INDEX idPartenaire ON service_apres_vente (idPartenaire)');
        $this->addSql('CREATE INDEX fk_id_achats ON service_apres_vente (idAchats)');
        $this->addSql('CREATE UNIQUE INDEX adresseEmail ON utilisateur (adresseEmail)');
    }
}
