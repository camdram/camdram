<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130102223523 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE acts_time_period_groups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, long_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_shows_time_periods_links (show_id INT NOT NULL, timeperiod_id INT NOT NULL, INDEX IDX_79BA6A0ED0C1FC64 (show_id), INDEX IDX_79BA6A0E66CE7C72 (timeperiod_id), PRIMARY KEY(show_id, timeperiod_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE acts_shows_time_periods_links ADD CONSTRAINT FK_79BA6A0ED0C1FC64 FOREIGN KEY (show_id) REFERENCES acts_shows (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_shows_time_periods_links ADD CONSTRAINT FK_79BA6A0E66CE7C72 FOREIGN KEY (timeperiod_id) REFERENCES acts_time_periods (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_time_periods ADD group_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE acts_time_periods ADD CONSTRAINT FK_51B4603BFE54D947 FOREIGN KEY (group_id) REFERENCES acts_time_period_groups (id)");
        $this->addSql("CREATE INDEX IDX_51B4603BFE54D947 ON acts_time_periods (group_id)");
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FE7EFD7106");
        $this->addSql("DROP INDEX IDX_1A1A53FE7EFD7106 ON acts_shows");
        $this->addSql("ALTER TABLE acts_shows ADD start_at DATETIME DEFAULT NULL, ADD end_at DATETIME DEFAULT NULL, DROP time_period_id");
        $this->addSql("ALTER TABLE acts_time_period_groups ADD start_at DATETIME NOT NULL, ADD end_at DATETIME NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE acts_time_period_groups DROP start_at, DROP end_at");
        $this->addSql("ALTER TABLE acts_time_periods DROP FOREIGN KEY FK_51B4603BFE54D947");
        $this->addSql("ALTER TABLE acts_roles ADD CONSTRAINT FK_C01F8C7DBF396750 FOREIGN KEY (id) REFERENCES acts_entities (id) ON DELETE CASCADE");
        $this->addSql("DROP TABLE acts_time_period_groups");
        $this->addSql("DROP TABLE acts_shows_time_periods_links");
        $this->addSql("ALTER TABLE acts_shows ADD time_period_id INT DEFAULT NULL, DROP start_at, DROP end_at");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE7EFD7106 FOREIGN KEY (time_period_id) REFERENCES acts_time_periods (id)");
        $this->addSql("CREATE INDEX IDX_1A1A53FE7EFD7106 ON acts_shows (time_period_id)");
        $this->addSql("DROP INDEX IDX_51B4603BFE54D947 ON acts_time_periods");
        $this->addSql("ALTER TABLE acts_time_periods DROP group_id");
    }
}
