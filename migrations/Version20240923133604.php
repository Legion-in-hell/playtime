<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240923133604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial migration.';
    }

    public function up(Schema $schema): void
    {
        // Cette méthode est vide car le schéma est déjà à jour
        $this->addSql('SELECT 1');
    }

    public function down(Schema $schema): void
    {
        // Cette méthode est vide car il n'y a rien à annuler
    }
}
