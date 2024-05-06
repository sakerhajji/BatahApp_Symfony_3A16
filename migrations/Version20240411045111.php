<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411045111 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commands DROP FOREIGN KEY FK_9A3E132C99DED506');
        $this->addSql('DROP INDEX IDX_9A3E132C99DED506 ON commands');
        $this->addSql('ALTER TABLE commands CHANGE id_client_id id_client INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commands ADD CONSTRAINT FK_9A3E132CE173B1B8 FOREIGN KEY (id_client) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_9A3E132CE173B1B8 ON commands (id_client)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commands DROP FOREIGN KEY FK_9A3E132CE173B1B8');
        $this->addSql('DROP INDEX IDX_9A3E132CE173B1B8 ON commands');
        $this->addSql('ALTER TABLE commands CHANGE id_client id_client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commands ADD CONSTRAINT FK_9A3E132C99DED506 FOREIGN KEY (id_client_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_9A3E132C99DED506 ON commands (id_client_id)');
    }
}
