<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131118001442 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE acts_time_periods DROP FOREIGN KEY FK_51B4603BFE54D947");
        $this->addSql("CREATE TABLE acts_week_names (id INT AUTO_INCREMENT NOT NULL, time_period_id INT DEFAULT NULL, short_name VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(128) DEFAULT NULL, start_at DATETIME NOT NULL, INDEX IDX_C467B35F7EFD7106 (time_period_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE acts_week_names ADD CONSTRAINT FK_C467B35F7EFD7106 FOREIGN KEY (time_period_id) REFERENCES acts_time_periods (id)");
        $this->addSql("DROP TABLE acts_time_period_groups");
        $this->addSql("DROP INDEX IDX_51B4603BFE54D947 ON acts_time_periods");
        $this->addSql("ALTER TABLE acts_time_periods ADD slug VARCHAR(128) DEFAULT NULL, DROP group_id, DROP holiday, DROP visible, CHANGE long_name full_name VARCHAR(255) NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE acts_time_period_groups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, long_name VARCHAR(255) NOT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, slug VARCHAR(128) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("DROP TABLE acts_week_names");
        $this->addSql("ALTER TABLE acts_time_periods ADD group_id INT DEFAULT NULL, ADD holiday TINYINT(1) NOT NULL, ADD visible TINYINT(1) NOT NULL, DROP slug, CHANGE full_name long_name VARCHAR(255) NOT NULL");
        $this->addSql("ALTER TABLE acts_time_periods ADD CONSTRAINT FK_51B4603BFE54D947 FOREIGN KEY (group_id) REFERENCES acts_time_period_groups (id)");
        $this->addSql("CREATE INDEX IDX_51B4603BFE54D947 ON acts_time_periods (group_id)");
    }
}
