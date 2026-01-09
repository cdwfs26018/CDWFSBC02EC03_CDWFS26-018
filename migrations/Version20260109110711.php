<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260109110711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY `FK_8F91ABF020A01F78`');
        $this->addSql('DROP INDEX IDX_8F91ABF020A01F78 ON avis');
        $this->addSql('ALTER TABLE avis DROP updated_at, DROP moderateur_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis ADD updated_at DATETIME NOT NULL, ADD moderateur_id BINARY(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT `FK_8F91ABF020A01F78` FOREIGN KEY (moderateur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF020A01F78 ON avis (moderateur_id)');
    }
}
