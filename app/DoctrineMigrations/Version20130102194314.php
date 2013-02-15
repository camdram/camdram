<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130102194314 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE acts_time_periods (id INT AUTO_INCREMENT NOT NULL, short_name VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, long_name VARCHAR(255) NOT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, holiday TINYINT(1) NOT NULL, visible TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE acts_users CHANGE forumnotify forumnotify TINYINT(1) DEFAULT NULL, CHANGE occupation occupation VARCHAR(255) DEFAULT NULL, CHANGE graduation graduation VARCHAR(255) DEFAULT NULL, CHANGE tel tel VARCHAR(50) DEFAULT NULL, CHANGE dbemail dbemail TINYINT(1) DEFAULT NULL, CHANGE dbphone dbphone TINYINT(1) DEFAULT NULL, CHANGE threadmessages threadmessages TINYINT(1) DEFAULT NULL");
        $this->addSql("ALTER TABLE acts_shows ADD time_period_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE7EFD7106 FOREIGN KEY (time_period_id) REFERENCES acts_time_periods (id)");
        $this->addSql("CREATE INDEX IDX_1A1A53FE7EFD7106 ON acts_shows (time_period_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FE7EFD7106");
        $this->addSql("DROP TABLE acts_time_periods");
        $this->addSql("DROP INDEX IDX_1A1A53FE7EFD7106 ON acts_shows");
        $this->addSql("ALTER TABLE acts_shows DROP time_period_id");
        $this->addSql("ALTER TABLE acts_users CHANGE occupation occupation VARCHAR(255) NOT NULL, CHANGE graduation graduation VARCHAR(255) NOT NULL, CHANGE tel tel VARCHAR(50) NOT NULL, CHANGE dbemail dbemail TINYINT(1) NOT NULL, CHANGE dbphone dbphone TINYINT(1) NOT NULL, CHANGE forumnotify forumnotify TINYINT(1) NOT NULL, CHANGE threadmessages threadmessages TINYINT(1) NOT NULL");
    }
}
