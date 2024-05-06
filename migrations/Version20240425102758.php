<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425102758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket CHANGE remise remise VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE comment ADD parent_id INT DEFAULT NULL, CHANGE commentaire commentaire VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C727ACA70 FOREIGN KEY (parent_id) REFERENCES comment (id)');
        $this->addSql('CREATE INDEX IDX_9474526C727ACA70 ON comment (parent_id)');
        $this->addSql('ALTER TABLE contact DROP name, DROP email, DROP description');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FED8EF5D7');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FEB0C61EC');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FED8EF5D7 FOREIGN KEY (idProduits) REFERENCES produits (idProduit)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FEB0C61EC FOREIGN KEY (idLocations) REFERENCES location (idLocation)');
        $this->addSql('ALTER TABLE produits CHANGE photo photo VARCHAR(255) NOT NULL, CHANGE nombreDeVues nombreDeVues INT NOT NULL');
        $this->addSql('ALTER TABLE views DROP FOREIGN KEY FK_11F09C87F347EFB');
        $this->addSql('ALTER TABLE views DROP FOREIGN KEY FK_11F09C87FB88E14F');
        $this->addSql('ALTER TABLE views ADD CONSTRAINT FK_11F09C87F347EFB FOREIGN KEY (produit_id) REFERENCES produits (idProduit)');
        $this->addSql('ALTER TABLE views ADD CONSTRAINT FK_11F09C87FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket CHANGE remise remise VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C727ACA70');
        $this->addSql('DROP INDEX IDX_9474526C727ACA70 ON comment');
        $this->addSql('ALTER TABLE comment DROP parent_id, CHANGE commentaire commentaire VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE contact ADD name VARCHAR(255) DEFAULT NULL, ADD email VARCHAR(255) DEFAULT NULL, ADD description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FED8EF5D7');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FEB0C61EC');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FED8EF5D7 FOREIGN KEY (idProduits) REFERENCES produits (idProduit) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FEB0C61EC FOREIGN KEY (idLocations) REFERENCES location (idLocation) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produits CHANGE photo photo VARCHAR(255) DEFAULT NULL, CHANGE nombreDeVues nombreDeVues INT DEFAULT NULL');
        $this->addSql('ALTER TABLE views DROP FOREIGN KEY FK_11F09C87FB88E14F');
        $this->addSql('ALTER TABLE views DROP FOREIGN KEY FK_11F09C87F347EFB');
        $this->addSql('ALTER TABLE views ADD CONSTRAINT FK_11F09C87FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE views ADD CONSTRAINT FK_11F09C87F347EFB FOREIGN KEY (produit_id) REFERENCES produits (idProduit) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
