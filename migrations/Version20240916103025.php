<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240916103025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE establishment ADD CONSTRAINT FK_DBEFB1EE8565851 FOREIGN KEY (establishment_id) REFERENCES establishment (id)');
        $this->addSql('CREATE INDEX IDX_DBEFB1EE8565851 ON establishment (establishment_id)');
        $this->addSql('ALTER TABLE reservation ADD establishment_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849558565851 FOREIGN KEY (establishment_id) REFERENCES establishment (id)');
        $this->addSql('CREATE INDEX IDX_42C849558565851 ON reservation (establishment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE establishment DROP FOREIGN KEY FK_DBEFB1EE8565851');
        $this->addSql('DROP INDEX IDX_DBEFB1EE8565851 ON establishment');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849558565851');
        $this->addSql('DROP INDEX IDX_42C849558565851 ON reservation');
        $this->addSql('ALTER TABLE reservation DROP establishment_id');
    }
}
