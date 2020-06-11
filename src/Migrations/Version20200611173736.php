<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200611173736 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Initial build';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE map (id INT AUTO_INCREMENT NOT NULL, library_id INT NOT NULL, code VARCHAR(24) NOT NULL, label VARCHAR(255) NOT NULL, image_original_filename VARCHAR(255) NOT NULL, image_filename VARCHAR(255) NOT NULL, image_height SMALLINT UNSIGNED NOT NULL, image_width SMALLINT UNSIGNED NOT NULL, INDEX IDX_93ADAABBFE2541D7 (library_id), UNIQUE INDEX code_unique (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE library (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(24) NOT NULL, label VARCHAR(255) NOT NULL, UNIQUE INDEX code_unique (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shelf (id INT AUTO_INCREMENT NOT NULL, map_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, start_call_number VARCHAR(255) NOT NULL, end_call_number VARCHAR(255) NOT NULL, start_sort_call_number VARCHAR(255) NOT NULL, end_sort_call_number VARCHAR(255) NOT NULL, x SMALLINT UNSIGNED NOT NULL, y SMALLINT UNSIGNED NOT NULL, orientation SMALLINT UNSIGNED NOT NULL, INDEX IDX_A5475BE353C55F64 (map_id), INDEX callno_idx (start_sort_call_number, end_sort_call_number), UNIQUE INDEX code_unique (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE map ADD CONSTRAINT FK_93ADAABBFE2541D7 FOREIGN KEY (library_id) REFERENCES library (id)');
        $this->addSql('ALTER TABLE shelf ADD CONSTRAINT FK_A5475BE353C55F64 FOREIGN KEY (map_id) REFERENCES map (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE shelf DROP FOREIGN KEY FK_A5475BE353C55F64');
        $this->addSql('ALTER TABLE map DROP FOREIGN KEY FK_93ADAABBFE2541D7');
        $this->addSql('DROP TABLE map');
        $this->addSql('DROP TABLE library');
        $this->addSql('DROP TABLE shelf');
    }
}
