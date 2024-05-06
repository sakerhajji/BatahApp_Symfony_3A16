<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411044853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE command_articles (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, command_id INT DEFAULT NULL, INDEX IDX_8631E8F97294869C (article_id), INDEX IDX_8631E8F933E1689A (command_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE command_articles ADD CONSTRAINT FK_8631E8F97294869C FOREIGN KEY (article_id) REFERENCES produits (idProduit)');
        $this->addSql('ALTER TABLE command_articles ADD CONSTRAINT FK_8631E8F933E1689A FOREIGN KEY (command_id) REFERENCES commands (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE command_articles DROP FOREIGN KEY FK_8631E8F97294869C');
        $this->addSql('ALTER TABLE command_articles DROP FOREIGN KEY FK_8631E8F933E1689A');
        $this->addSql('DROP TABLE command_articles');
    }
}
