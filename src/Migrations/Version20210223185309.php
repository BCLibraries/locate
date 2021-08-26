<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210223185309 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Your description';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE library (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(24) NOT NULL, label VARCHAR(255) NOT NULL, INDEX index (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE map (id INT AUTO_INCREMENT NOT NULL, library_id INT NOT NULL, code VARCHAR(24) NOT NULL, label VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, INDEX IDX_93ADAABBFE2541D7 (library_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shelf (id INT AUTO_INCREMENT NOT NULL, map_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, start_call_number VARCHAR(255) NOT NULL, end_call_number VARCHAR(255) NOT NULL, start_sort_call_number VARCHAR(255) NOT NULL, end_sort_call_number VARCHAR(255) NOT NULL, INDEX IDX_A5475BE353C55F64 (map_id), INDEX callno_idx (start_sort_call_number, end_sort_call_number), UNIQUE INDEX code_unique (code), UNIQUE INDEX shelf_map_id_code_uindex(map_id, code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE map ADD CONSTRAINT FK_93ADAABBFE2541D7 FOREIGN KEY (library_id) REFERENCES library (id)');
        $this->addSql('ALTER TABLE shelf ADD CONSTRAINT FK_A5475BE353C55F64 FOREIGN KEY (map_id) REFERENCES map (id)');

        $initial_data_sql = <<<SQL
INSERT INTO `library` VALUES (1,'onl','O\'Neill Library'),(2,'law','Law School Library');
INSERT INTO `map` VALUES (1,1,'floor-5','Level 5','onl-floor-5'),(2,1,'floor-4-south','Level 4 South','onl-floor-4-south'),(3,1,'floor-4-north','Level 4 North','onl-floor-4-north'),(4,1,'floor-3','Level 3','onl-floor-3'),(5,2,'floor-3','Level 3','law-floor-3'),(6,2,'floor-4','Level 4','law-floor-4');
SQL;

        $this->addSql($initial_data_sql);
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE map DROP FOREIGN KEY FK_93ADAABBFE2541D7');
        $this->addSql('ALTER TABLE shelf DROP FOREIGN KEY FK_A5475BE353C55F64');
        $this->addSql('DROP TABLE library');
        $this->addSql('DROP TABLE map');
        $this->addSql('DROP TABLE shelf');
    }
}
