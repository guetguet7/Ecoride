<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204113120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rides ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rides ADD CONSTRAINT FK_9D4620A3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9D4620A3A76ED395 ON rides (user_id)');
        $this->addSql('DROP INDEX IDX_voiture_user ON voiture');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rides DROP FOREIGN KEY FK_9D4620A3A76ED395');
        $this->addSql('DROP INDEX IDX_9D4620A3A76ED395 ON rides');
        $this->addSql('ALTER TABLE rides DROP user_id');
        $this->addSql('CREATE INDEX IDX_voiture_user ON voiture (user_id)');
    }
}
