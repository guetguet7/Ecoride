<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204112639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE voiture DROP INDEX UNIQ_E9E2810FA76ED395, ADD INDEX IDX_E9E2810FA76ED395 (user_id)');
        $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY `FK_voiture_user`');
        $this->addSql('DROP INDEX UNIQ_voiture_user ON voiture');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE voiture DROP INDEX IDX_E9E2810FA76ED395, ADD UNIQUE INDEX UNIQ_E9E2810FA76ED395 (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_voiture_user ON voiture (user_id)');
    }
}
