<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130102010947 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE acts_termdates CHANGE firstweek firstweek INT NOT NULL, CHANGE lastweek lastweek INT NOT NULL");
        $this->addSql("ALTER TABLE acts_groups ADD short_name VARCHAR(30) NOT NULL");
        $this->addSql("ALTER TABLE acts_groups ADD menu_name VARCHAR(30) NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE acts_groups DROP menu_name");
        $this->addSql("ALTER TABLE acts_groups DROP short_name");
        $this->addSql("ALTER TABLE acts_termdates CHANGE firstweek firstweek TINYINT(1) NOT NULL, CHANGE lastweek lastweek TINYINT(1) NOT NULL");
    }
}
