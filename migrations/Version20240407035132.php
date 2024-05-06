<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240407035132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE achats (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE basket (id_client INT DEFAULT NULL, id_produit INT DEFAULT NULL, idBasket INT AUTO_INCREMENT NOT NULL, remise VARCHAR(255) NOT NULL, date_ajout DATETIME NOT NULL, INDEX IDX_2246507BE173B1B8 (id_client), INDEX IDX_2246507BF7384557 (id_produit), PRIMARY KEY(idBasket)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE encheres (idEnchere INT AUTO_INCREMENT NOT NULL, dateDebut DATE DEFAULT NULL, dateFin DATE DEFAULT NULL, Status TINYINT(1) DEFAULT NULL, prixMin DOUBLE PRECISION DEFAULT NULL, prixMax DOUBLE PRECISION DEFAULT NULL, prixActuelle DOUBLE PRECISION DEFAULT NULL, nbrParticipants INT NOT NULL, idProduit INT DEFAULT NULL, INDEX IDX_8B89031D391C87D5 (idProduit), PRIMARY KEY(idEnchere)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (idImage INT AUTO_INCREMENT NOT NULL, url VARCHAR(250) NOT NULL, idProduits INT DEFAULT NULL, idLocations INT DEFAULT NULL, INDEX IDX_C53D045FED8EF5D7 (idProduits), INDEX IDX_C53D045FEB0C61EC (idLocations), PRIMARY KEY(idImage)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT DEFAULT NULL, idLocation INT AUTO_INCREMENT NOT NULL, type VARCHAR(300) NOT NULL, description VARCHAR(300) NOT NULL, prix DOUBLE PRECISION NOT NULL, adresse VARCHAR(300) NOT NULL, disponibilite VARCHAR(255) NOT NULL, INDEX IDX_5E9E89CBBF396750 (id), PRIMARY KEY(idLocation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partenaires (idPartenaire INT AUTO_INCREMENT NOT NULL, nom VARCHAR(20) NOT NULL, type VARCHAR(20) NOT NULL, adresse VARCHAR(20) NOT NULL, telephone INT NOT NULL, email VARCHAR(50) NOT NULL, logo VARCHAR(200) NOT NULL, points INT DEFAULT NULL, PRIMARY KEY(idPartenaire)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produits (idProduit INT AUTO_INCREMENT NOT NULL, type VARCHAR(300) NOT NULL, description VARCHAR(300) NOT NULL, prix DOUBLE PRECISION NOT NULL, labelle VARCHAR(300) NOT NULL, status VARCHAR(255) NOT NULL, periodeGarantie INT NOT NULL, photo VARCHAR(255) NOT NULL, video VARCHAR(250) DEFAULT NULL, localisation VARCHAR(255) NOT NULL, nombreDeVues INT NOT NULL, idUtilisateur INT DEFAULT NULL, INDEX IDX_BE2DDF8C5D419CCB (idUtilisateur), PRIMARY KEY(idProduit)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ratings (id_rating INT AUTO_INCREMENT NOT NULL, id_produit INT DEFAULT NULL, id_user INT DEFAULT NULL, rating DOUBLE PRECISION NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, INDEX IDX_CEB607C9F7384557 (id_produit), INDEX IDX_CEB607C96B3CA4B (id_user), PRIMARY KEY(id_rating)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation_enchere (idReservation INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(idReservation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_apres_vente (idService INT AUTO_INCREMENT NOT NULL, description VARCHAR(200) NOT NULL, type VARCHAR(50) NOT NULL, date DATETIME NOT NULL, status TINYINT(1) NOT NULL, idAchats INT DEFAULT NULL, idPartenaire INT DEFAULT NULL, PRIMARY KEY(idService)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, idGoogle VARCHAR(255) DEFAULT NULL, nomUtilisateur VARCHAR(30) DEFAULT NULL, prenomUtilisateur VARCHAR(50) DEFAULT NULL, sexe CHAR(1) DEFAULT NULL, dateDeNaissance DATE DEFAULT NULL, adresseEmail VARCHAR(100) DEFAULT NULL, motDePasse VARCHAR(30) DEFAULT NULL, adressePostale VARCHAR(60) DEFAULT NULL, numeroTelephone VARCHAR(30) DEFAULT NULL, numeroCin VARCHAR(9) DEFAULT NULL, pays VARCHAR(50) DEFAULT NULL, nbrProduitAchat INT DEFAULT NULL, nbrProduitVendu INT DEFAULT NULL, nbrProduit INT DEFAULT NULL, nbrPoint INT DEFAULT NULL, languePreferree VARCHAR(50) DEFAULT NULL, evaluationUtilisateur DOUBLE PRECISION DEFAULT NULL, statutVerificationCompte TINYINT(1) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, dateInscription DATETIME DEFAULT NULL, role CHAR(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE views (utilisateur_id INT DEFAULT NULL, produit_id INT DEFAULT NULL, idViews INT AUTO_INCREMENT NOT NULL, likes INT DEFAULT 0, dislikes INT DEFAULT 0, INDEX IDX_11F09C87FB88E14F (utilisateur_id), INDEX IDX_11F09C87F347EFB (produit_id), PRIMARY KEY(idViews)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507BE173B1B8 FOREIGN KEY (id_client) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507BF7384557 FOREIGN KEY (id_produit) REFERENCES produits (idProduit)');
        $this->addSql('ALTER TABLE encheres ADD CONSTRAINT FK_8B89031D391C87D5 FOREIGN KEY (idProduit) REFERENCES produits (idProduit)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FED8EF5D7 FOREIGN KEY (idProduits) REFERENCES produits (idProduit)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FEB0C61EC FOREIGN KEY (idLocations) REFERENCES location (idLocation)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CBBF396750 FOREIGN KEY (id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT FK_BE2DDF8C5D419CCB FOREIGN KEY (idUtilisateur) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE ratings ADD CONSTRAINT FK_CEB607C9F7384557 FOREIGN KEY (id_produit) REFERENCES produits (idProduit)');
        $this->addSql('ALTER TABLE ratings ADD CONSTRAINT FK_CEB607C96B3CA4B FOREIGN KEY (id_user) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE views ADD CONSTRAINT FK_11F09C87FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE views ADD CONSTRAINT FK_11F09C87F347EFB FOREIGN KEY (produit_id) REFERENCES produits (idProduit)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507BE173B1B8');
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507BF7384557');
        $this->addSql('ALTER TABLE encheres DROP FOREIGN KEY FK_8B89031D391C87D5');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FED8EF5D7');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FEB0C61EC');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CBBF396750');
        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY FK_BE2DDF8C5D419CCB');
        $this->addSql('ALTER TABLE ratings DROP FOREIGN KEY FK_CEB607C9F7384557');
        $this->addSql('ALTER TABLE ratings DROP FOREIGN KEY FK_CEB607C96B3CA4B');
        $this->addSql('ALTER TABLE views DROP FOREIGN KEY FK_11F09C87FB88E14F');
        $this->addSql('ALTER TABLE views DROP FOREIGN KEY FK_11F09C87F347EFB');
        $this->addSql('DROP TABLE achats');
        $this->addSql('DROP TABLE basket');
        $this->addSql('DROP TABLE encheres');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE partenaires');
        $this->addSql('DROP TABLE produits');
        $this->addSql('DROP TABLE ratings');
        $this->addSql('DROP TABLE reservation_enchere');
        $this->addSql('DROP TABLE service_apres_vente');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE views');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
