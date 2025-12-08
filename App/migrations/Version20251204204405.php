<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204204405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis ADD created_at DATETIME NOT NULL, ADD author_id INT NOT NULL, ADD driver_id INT NOT NULL, ADD ride_id INT NOT NULL, CHANGE commentaire commentaire VARCHAR(255) DEFAULT NULL, CHANGE note note SMALLINT DEFAULT 0 NOT NULL, CHANGE statut statut VARCHAR(20) DEFAULT \'pending\' NOT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0C3423909 FOREIGN KEY (driver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0302A8A70 FOREIGN KEY (ride_id) REFERENCES rides (id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0F675F31B ON avis (author_id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0C3423909 ON avis (driver_id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0302A8A70 ON avis (ride_id)');
        $this->addSql('ALTER TABLE user CHANGE rating rating DOUBLE PRECISION DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0F675F31B');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0C3423909');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0302A8A70');
        $this->addSql('DROP INDEX IDX_8F91ABF0F675F31B ON avis');
        $this->addSql('DROP INDEX IDX_8F91ABF0C3423909 ON avis');
        $this->addSql('DROP INDEX IDX_8F91ABF0302A8A70 ON avis');
        $this->addSql('ALTER TABLE avis DROP created_at, DROP author_id, DROP driver_id, DROP ride_id, CHANGE commentaire commentaire VARCHAR(255) NOT NULL, CHANGE note note VARCHAR(255) NOT NULL, CHANGE statut statut VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE rating rating DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
    }
}
