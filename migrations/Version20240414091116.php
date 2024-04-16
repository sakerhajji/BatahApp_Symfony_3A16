<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240414091116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, id_client INT DEFAULT NULL, id_produit INT DEFAULT NULL, commentaire VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_9474526CE173B1B8 (id_client), INDEX IDX_9474526CF7384557 (id_produit), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CE173B1B8 FOREIGN KEY (id_client) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF7384557 FOREIGN KEY (id_produit) REFERENCES produits (idProduit)');
        $this->addSql('ALTER TABLE basket CHANGE remise remise VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CE173B1B8');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF7384557');
        $this->addSql('DROP TABLE comment');
        $this->addSql('ALTER TABLE basket CHANGE remise remise VARCHAR(255) DEFAULT NULL');
    }
}
