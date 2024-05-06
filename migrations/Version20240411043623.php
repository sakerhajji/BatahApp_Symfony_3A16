<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411043623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commands (id INT AUTO_INCREMENT NOT NULL, id_client_id INT DEFAULT NULL, date_commande DATETIME NOT NULL, mode_livraison VARCHAR(30) DEFAULT NULL, mode_paiement VARCHAR(30) DEFAULT NULL, cout_totale DOUBLE PRECISION DEFAULT NULL, etat_commande VARCHAR(30) DEFAULT \'En attente\', adresse VARCHAR(30) NOT NULL, INDEX IDX_9A3E132C99DED506 (id_client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commands ADD CONSTRAINT FK_9A3E132C99DED506 FOREIGN KEY (id_client_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE basket CHANGE remise remise VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commands DROP FOREIGN KEY FK_9A3E132C99DED506');
        $this->addSql('DROP TABLE commands');
        $this->addSql('ALTER TABLE basket CHANGE remise remise VARCHAR(255) DEFAULT NULL');
    }
}
