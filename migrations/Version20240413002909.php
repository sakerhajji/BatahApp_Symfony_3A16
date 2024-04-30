<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240413002909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE forum (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, created_at DATE NOT NULL, username VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_apres_vente CHANGE description description VARCHAR(255) NOT NULL, CHANGE type type VARCHAR(255) NOT NULL, CHANGE status status TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE service_apres_vente ADD CONSTRAINT FK_E8A0B369FA313AD4 FOREIGN KEY (idAchats) REFERENCES achats (idAchats)');
        $this->addSql('ALTER TABLE service_apres_vente ADD CONSTRAINT FK_E8A0B369B00BBD99 FOREIGN KEY (idPartenaire) REFERENCES partenaires (idPartenaire)');
        $this->addSql('DROP INDEX fk_id_achats ON service_apres_vente');
        $this->addSql('CREATE INDEX IDX_E8A0B369FA313AD4 ON service_apres_vente (idAchats)');
        $this->addSql('DROP INDEX idpartenaire ON service_apres_vente');
        $this->addSql('CREATE INDEX IDX_E8A0B369B00BBD99 ON service_apres_vente (idPartenaire)');
        $this->addSql('DROP INDEX adresseEmail ON utilisateur');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE forum');
        $this->addSql('ALTER TABLE service_apres_vente DROP FOREIGN KEY FK_E8A0B369FA313AD4');
        $this->addSql('ALTER TABLE service_apres_vente DROP FOREIGN KEY FK_E8A0B369B00BBD99');
        $this->addSql('ALTER TABLE service_apres_vente DROP FOREIGN KEY FK_E8A0B369FA313AD4');
        $this->addSql('ALTER TABLE service_apres_vente DROP FOREIGN KEY FK_E8A0B369B00BBD99');
        $this->addSql('ALTER TABLE service_apres_vente CHANGE description description VARCHAR(200) NOT NULL, CHANGE type type VARCHAR(50) NOT NULL, CHANGE status status TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('DROP INDEX idx_e8a0b369b00bbd99 ON service_apres_vente');
        $this->addSql('CREATE INDEX idPartenaire ON service_apres_vente (idPartenaire)');
        $this->addSql('DROP INDEX idx_e8a0b369fa313ad4 ON service_apres_vente');
        $this->addSql('CREATE INDEX fk_id_achats ON service_apres_vente (idAchats)');
        $this->addSql('ALTER TABLE service_apres_vente ADD CONSTRAINT FK_E8A0B369FA313AD4 FOREIGN KEY (idAchats) REFERENCES achats (idAchats)');
        $this->addSql('ALTER TABLE service_apres_vente ADD CONSTRAINT FK_E8A0B369B00BBD99 FOREIGN KEY (idPartenaire) REFERENCES partenaires (idPartenaire)');
        $this->addSql('CREATE UNIQUE INDEX adresseEmail ON utilisateur (adresseEmail)');
    }
}
