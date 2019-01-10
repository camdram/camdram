<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180721183403 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE acts_name_aliases');
        $this->addSql('DROP TABLE acts_similar_names');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE acts_name_aliases (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_355DA778217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_similar_names (id INT AUTO_INCREMENT NOT NULL, name1 VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, name2 VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, equivalence TINYINT(1) NOT NULL, UNIQUE INDEX names_unique (name1, name2), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acts_name_aliases ADD CONSTRAINT FK_355DA778217BBB47 FOREIGN KEY (person_id) REFERENCES acts_people_data (id)');
    }
}
