<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130223040015 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE acts_shows CHANGE author author VARCHAR(255) DEFAULT NULL, CHANGE excludedate excludedate DATE DEFAULT NULL, CHANGE venue venue VARCHAR(255) DEFAULT NULL, CHANGE prices prices VARCHAR(255) DEFAULT NULL, CHANGE authorizeid authorizeid INT DEFAULT NULL, CHANGE bookingcode bookingcode VARCHAR(255) DEFAULT NULL, CHANGE primaryref primaryref INT DEFAULT NULL");
        $this->addSql("UPDATE `acts_shows` SET excludedate = NULL WHERE excludedate = '0000-00-00 00:00:00'");
        $this->addSql("ALTER TABLE acts_performances CHANGE excludedate excludedate DATE DEFAULT NULL, CHANGE venue venue VARCHAR(255) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE acts_shows CHANGE author author VARCHAR(255) NOT NULL, CHANGE excludedate excludedate DATETIME NOT NULL, CHANGE venue venue VARCHAR(255) NOT NULL, CHANGE prices prices VARCHAR(255) NOT NULL, CHANGE authorizeid authorizeid INT NOT NULL, CHANGE bookingcode bookingcode VARCHAR(255) NOT NULL, CHANGE primaryref primaryref INT NOT NULL");
        $this->addSql("ALTER TABLE acts_performances CHANGE excludedate excludedate DATE NOT NULL, CHANGE venue venue VARCHAR(255) NOT NULL");
    }
}
