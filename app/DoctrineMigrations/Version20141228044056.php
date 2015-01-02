<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141228044056 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_support CHANGE supportid supportid INT DEFAULT NULL');
        $this->addSql('UPDATE acts_support AS s LEFT JOIN acts_support AS u ON s.supportid = u.id SET s.supportid = NULL WHERE u.id IS NULL');
        $this->addSql('ALTER TABLE acts_support ADD CONSTRAINT FK_A6F619725B919408 FOREIGN KEY (supportid) REFERENCES acts_support (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_support DROP FOREIGN KEY FK_A6F619725B919408');
        $this->addSql('ALTER TABLE acts_support CHANGE supportid supportid INT NOT NULL');
    }
}
