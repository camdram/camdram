<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180616162120 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_shows DROP excludedate, DROP entered, DROP bookingcode');
        $this->addSql('DELETE FROM acts_support WHERE supportid = 0');
        $this->addSql('ALTER TABLE acts_support ADD CONSTRAINT FK_A6F619725B919408 FOREIGN KEY (supportid) REFERENCES acts_support (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_shows ADD excludedate DATE DEFAULT NULL, ADD entered TINYINT(1) NOT NULL, ADD bookingcode VARCHAR(255) DEFAULT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE acts_support DROP FOREIGN KEY FK_A6F619725B919408');
    }
}
