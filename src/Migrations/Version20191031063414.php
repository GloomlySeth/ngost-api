<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191031063414 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, created_user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, image VARCHAR(255) NOT NULL, short_desc VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, updated_at DATETIME NOT NULL, INDEX IDX_1DD39950E104C1D3 (created_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD39950E104C1D3 FOREIGN KEY (created_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE pages CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL, CHANGE slug slug VARCHAR(191) NOT NULL, CHANGE name name VARCHAR(191) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2074E575989D9B62 ON pages (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2074E5755E237E06 ON pages (name)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE news');
        $this->addSql('DROP INDEX UNIQ_2074E575989D9B62 ON pages');
        $this->addSql('DROP INDEX UNIQ_2074E5755E237E06 ON pages');
        $this->addSql('ALTER TABLE pages CHANGE updated_at updated_at DATETIME NOT NULL, CHANGE deleted_at deleted_at DATETIME NOT NULL, CHANGE slug slug VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
