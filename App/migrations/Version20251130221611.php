<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251130221611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD nom VARCHAR(255) DEFAULT NULL, ADD prenom VARCHAR(50) NOT NULL, ADD password VARCHAR(255) NOT NULL, ADD telephone VARCHAR(255) NOT NULL, ADD adresse VARCHAR(255) NOT NULL, ADD date_naissance VARCHAR(255) NOT NULL, ADD photo LONGBLOB NOT NULL, DROP pass, DROP credit, DROP role');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD pass VARCHAR(255) NOT NULL, ADD credit INT NOT NULL, ADD role VARCHAR(255) NOT NULL, DROP nom, DROP prenom, DROP password, DROP telephone, DROP adresse, DROP date_naissance, DROP photo');
    }
}
