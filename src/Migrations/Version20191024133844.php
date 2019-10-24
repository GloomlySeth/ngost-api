<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191024133844 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pages ADD slug VARCHAR(255) NOT NULL, ADD header VARCHAR(255) NOT NULL, ADD name VARCHAR(255) NOT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2074E575989D9B62 ON pages (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2074E5755E237E06 ON pages (name)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_2074E575989D9B62 ON pages');
        $this->addSql('DROP INDEX UNIQ_2074E5755E237E06 ON pages');
        $this->addSql('ALTER TABLE pages DROP slug, DROP header, DROP name, CHANGE title title VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
