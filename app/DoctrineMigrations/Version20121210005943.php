<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;

use Doctrine\DBAL\Migrations\AbstractMigration;

/**
 * Migration for user/authentication stuff
 */
class Version20121210005943 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('SET foreign_key_checks = 0');
        $this->addSql("CREATE TABLE acts_entities (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, facebook_id INT DEFAULT NULL, twitter_id INT DEFAULT NULL, entity_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_societies_new (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_venues (id INT NOT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");

        /*Maps shows onto ids 1...4799
               societies/venues onto ids 4800...4999
               people onto ids 5000...15000
        */
        $this->addSql("INSERT INTO acts_entities (id, name, description, entity_type) SELECT id, title, description, 'show' FROM acts_shows");

        $this->addSql("UPDATE acts_societies SET id = id + 4800 ORDER BY id DESC");
        $this->addSql("INSERT INTO acts_entities (id, name, description, entity_type) SELECT id, name, description, 'society' FROM acts_societies WHERE type = 0");
        $this->addSql("INSERT INTO acts_entities (id, name, description, entity_type) SELECT id, name, description, 'venue' FROM acts_societies WHERE type = 1");
        $this->addSql("INSERT INTO acts_societies_new (id) SELECT id FROM acts_societies WHERE type = 0");
        $this->addSql("INSERT INTO acts_venues (id) SELECT id FROM acts_societies WHERE type = 1");
        $this->addSql("UPDATE acts_shows SET venid = venid + 4800 WHERE venid > 0");
        $this->addSql("UPDATE acts_shows SET socid = socid + 4800 WHERE socid > 0");
        $this->addSql("UPDATE acts_access SET rid = rid + 4800 WHERE rid > 0 AND type = 'society'");
        $this->addSql("UPDATE acts_applications SET socid = socid + 4800 WHERE socid > 0");
        $this->addSql("UPDATE acts_events SET socid = socid + 4800 WHERE socid > 0");
        $this->addSql("UPDATE acts_pendingaccess SET rid = rid + 4800 WHERE rid > 0 AND type = 'society'");
        $this->addSql("UPDATE acts_performances SET venid = venid + 4800 WHERE venid > 0");

        $this->addSql("UPDATE acts_people_data SET id = id + 5000 ORDER BY id DESC");
        $this->addSql("UPDATE acts_people_data SET mapto = mapto + 5000 WHERE mapto > 0 ORDER BY id DESC");
        $this->addSql("INSERT INTO acts_entities (id, name, description, entity_type) SELECT id, name, '', 'person' FROM acts_people_data");
        $this->addSql("UPDATE acts_shows_people_link SET pid = pid + 5000 WHERE pid > 0");


        $this->addSql("ALTER TABLE acts_societies_new ADD CONSTRAINT FK_5A6F6504BF396750 FOREIGN KEY (id) REFERENCES acts_entities (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_venues ADD CONSTRAINT FK_4EEC599DBF396750 FOREIGN KEY (id) REFERENCES acts_entities (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_societies ADD CONSTRAINT FK_D8C3764BF396750 FOREIGN KEY (id) REFERENCES acts_entities (id) ON DELETE CASCADE");

        $this->addSql("ALTER TABLE acts_people_data ADD CONSTRAINT FK_567E1F8FBF396750 FOREIGN KEY (id) REFERENCES acts_entities (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FEBF396750 FOREIGN KEY (id) REFERENCES acts_entities (id) ON DELETE CASCADE");
        $this->addSql('SET foreign_key_checks = 1');

    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

        $this->addSql('SET foreign_key_checks = 0');

        $this->addSql("ALTER TABLE acts_societies DROP FOREIGN KEY FK_D8C3764BF396750");
        $this->addSql("ALTER TABLE acts_societies_new DROP FOREIGN KEY FK_5A6F6504BF396750");
        $this->addSql("ALTER TABLE acts_venues DROP FOREIGN KEY FK_4EEC599DBF396750");
        $this->addSql("ALTER TABLE acts_people_data DROP FOREIGN KEY FK_567E1F8FBF396750");
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FEBF396750");

        $this->addSql("UPDATE acts_societies SET id = id - 4800");
        $this->addSql("UPDATE acts_shows SET venid = venid - 4800 WHERE venid > 0");
        $this->addSql("UPDATE acts_shows SET socid = socid - 4800 WHERE socid > 0");
        $this->addSql("UPDATE acts_access SET rid = rid - 4800 WHERE rid > 0 AND type = 'society'");
        $this->addSql("UPDATE acts_applications SET socid = socid - 4800 WHERE socid > 0");
        $this->addSql("UPDATE acts_events SET socid = socid - 4800 WHERE socid > 0");
        $this->addSql("UPDATE acts_pendingaccess SET rid = rid - 4800 WHERE rid > 0 AND type = 'society'");
        $this->addSql("UPDATE acts_performances SET venid = venid - 4800 WHERE venid > 0");
        $this->addSql("UPDATE acts_people_data SET id = id - 5000 ORDER BY id ASC");
        $this->addSql("UPDATE acts_people_data SET mapto = mapto - 5000 WHERE mapto > 0 ORDER BY id ASC");
        $this->addSql("UPDATE acts_shows_people_link SET pid = pid - 5000 WHERE pid > 0");

        $this->addSql("DROP TABLE acts_entities");
        $this->addSql("DROP TABLE acts_societies_new");
        $this->addSql("DROP TABLE acts_venues");
        $this->addSql('SET foreign_key_checks = 1');
    }
}
