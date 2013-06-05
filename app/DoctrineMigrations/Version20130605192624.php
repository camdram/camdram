<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130605192624 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");


        $this->addSql("ALTER TABLE acts_time_period_groups ADD slug VARCHAR(128) DEFAULT NULL");
        $this->addSql("DROP TABLE acts_shows_time_periods_links");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE acts_time_period_groups DROP slug");
        $this->addSql("CREATE TABLE acts_shows_time_periods_links (show_id INT NOT NULL, timeperiod_id INT NOT NULL, INDEX IDX_79BA6A0ED0C1FC64 (show_id), INDEX IDX_79BA6A0E66CE7C72 (timeperiod_id), PRIMARY KEY(show_id, timeperiod_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE acts_shows_time_periods_links ADD CONSTRAINT FK_79BA6A0E66CE7C72 FOREIGN KEY (timeperiod_id) REFERENCES acts_time_periods (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_shows_time_periods_links ADD CONSTRAINT FK_79BA6A0ED0C1FC64 FOREIGN KEY (show_id) REFERENCES acts_shows (id) ON DELETE CASCADE");
    }
}
