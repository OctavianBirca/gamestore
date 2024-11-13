<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241106110647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shop ADD image VARCHAR(255) DEFAULT NULL, ADD slug VARCHAR(255) NOT NULL, ADD description LONGTEXT DEFAULT NULL, ADD google_maps LONGTEXT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC6A4CA2989D9B62 ON shop (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_AC6A4CA2989D9B62 ON shop');
        $this->addSql('ALTER TABLE shop DROP image, DROP slug, DROP description, DROP google_maps');
    }
}
