<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20121213041940 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

        $this->addSql("CREATE TABLE acts_name_aliases (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_similar_names (id INT AUTO_INCREMENT NOT NULL, name1 VARCHAR(255) NOT NULL, name2 VARCHAR(255) NOT NULL, equivalence TINYINT(1) NOT NULL, UNIQUE INDEX names_unique (name1, name2), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");

        $this->addSql("ALTER TABLE `acts_events` CHANGE `socid` socid INT DEFAULT NULL");
        $this->addSql("ALTER TABLE `acts_users` ADD person_id INT DEFAULT NULL, ADD upgraded_at DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE acts_users ADD CONSTRAINT FK_62A20753217BBB47 FOREIGN KEY (person_id) REFERENCES acts_people_data (id)");
        $this->addSql("CREATE INDEX IDX_62A20753217BBB47 ON acts_users (person_id)");

        $this->addSql("ALTER TABLE acts_name_aliases ADD CONSTRAINT FK_355DA778217BBB47 FOREIGN KEY (person_id) REFERENCES acts_people_data (id)");
        $this->addSql("CREATE INDEX IDX_355DA778217BBB47 ON acts_name_aliases (person_id)");
        $this->addSql("ALTER TABLE `acts_shows` CHANGE venid venid INT DEFAULT NULL, CHANGE socid socid INT DEFAULT NULL");
        $this->addSql("UPDATE acts_shows AS s LEFT JOIN acts_societies AS v ON  s.venid = v.id SET s.venid = NULL WHERE v.id IS NULL");
        $this->addSql("UPDATE acts_shows AS s LEFT JOIN acts_societies AS c ON  s.socid = c.id SET s.socid = NULL WHERE c.id IS NULL");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE40A73EBA FOREIGN KEY (venid) REFERENCES acts_societies (id)");
        $this->addSql("CREATE INDEX IDX_1A1A53FE40A73EBA ON acts_shows (venid)");
        $this->addSql("ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D4E176C6");
        $this->addSql("UPDATE acts_performances AS p LEFT JOIN acts_societies AS v ON  p.venid = v.id SET p.venid = NULL WHERE v.id IS NULL");
        $this->addSql("ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D4E176C6 FOREIGN KEY (venid) REFERENCES acts_societies (id)");
        $this->addSql("UPDATE acts_events AS e LEFT JOIN acts_societies AS c ON  e.socid = c.id SET e.socid = NULL WHERE c.id IS NULL");
        $this->addSql("ALTER TABLE acts_events ADD CONSTRAINT FK_78452C7AAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id)");
        $this->addSql("UPDATE acts_applications AS a LEFT JOIN acts_societies AS c ON  a.socid = c.id SET a.socid = NULL WHERE c.id IS NULL");
        $this->addSql("ALTER TABLE acts_applications ADD CONSTRAINT FK_95ED3F0FAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id)");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FEAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id)");

        $this->addSql('SET foreign_key_checks = 0');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

        $this->addSql("ALTER TABLE acts_applications DROP FOREIGN KEY FK_95ED3F0FAF648A81");
        $this->addSql("ALTER TABLE acts_events DROP FOREIGN KEY FK_78452C7AAF648A81");
        $this->addSql("ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D4E176C6");
        $this->addSql("ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D4E176C6 FOREIGN KEY (venid) REFERENCES acts_societies (id)");
        $this->addSql("ALTER TABLE acts_users DROP FOREIGN KEY FK_62A20753217BBB47");
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FEAF648A81");

        $this->addSql("ALTER TABLE acts_name_aliases DROP FOREIGN KEY FK_355DA778217BBB47");
        $this->addSql("DROP INDEX `IDX_355DA778217BBB47` ON `acts_name_aliases`");
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FE40A73EBA");
        $this->addSql("ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B8063D9AB4A6");
        $this->addSql("DROP TABLE acts_name_aliases");
        $this->addSql("DROP TABLE acts_user_identities");
        $this->addSql("DROP TABLE acts_similar_names");
        $this->addSql("ALTER TABLE `acts_events` CHANGE `socid` socid INT NOT NULL");
        $this->addSql("DROP INDEX `IDX_1A1A53FE40A73EBA` ON `acts_shows`");
        $this->addSql("DROP INDEX `IDX_62A20753217BBB47` ON `acts_users`");
        $this->addSql("ALTER TABLE `acts_users` DROP person_id, DROP upgraded_at");
    }

}