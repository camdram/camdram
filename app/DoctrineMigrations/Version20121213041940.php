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

        $this->addSql("CREATE TABLE acts_user_identities (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, service VARCHAR(50) NOT NULL, remote_id VARCHAR(100) DEFAULT NULL, remote_user VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, token_secret VARCHAR(255) DEFAULT NULL, INDEX IDX_B4BCDC47A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_groups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_user_group_links (group_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C789B1F6FE54D947 (group_id), INDEX IDX_C789B1F6A76ED395 (user_id), PRIMARY KEY(group_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_classes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, class_type VARCHAR(200) NOT NULL, UNIQUE INDEX UNIQ_69DD750638A36066 (class_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_security_identities (id INT UNSIGNED AUTO_INCREMENT NOT NULL, identifier VARCHAR(200) NOT NULL, username TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8835EE78772E836AF85E0677 (identifier, username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_object_identities (id INT UNSIGNED AUTO_INCREMENT NOT NULL, parent_object_identity_id INT UNSIGNED DEFAULT NULL, class_id INT UNSIGNED NOT NULL, object_identifier VARCHAR(100) NOT NULL, entries_inheriting TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_9407E5494B12AD6EA000B10 (object_identifier, class_id), INDEX IDX_9407E54977FA751A (parent_object_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_object_identity_ancestors (object_identity_id INT UNSIGNED NOT NULL, ancestor_id INT UNSIGNED NOT NULL, INDEX IDX_825DE2993D9AB4A6 (object_identity_id), INDEX IDX_825DE299C671CEA1 (ancestor_id), PRIMARY KEY(object_identity_id, ancestor_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_entries (id INT UNSIGNED AUTO_INCREMENT NOT NULL, class_id INT UNSIGNED NOT NULL, object_identity_id INT UNSIGNED DEFAULT NULL, security_identity_id INT UNSIGNED NOT NULL, field_name VARCHAR(50) DEFAULT NULL, ace_order SMALLINT UNSIGNED NOT NULL, mask INT NOT NULL, granting TINYINT(1) NOT NULL, granting_strategy VARCHAR(30) NOT NULL, audit_success TINYINT(1) NOT NULL, audit_failure TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_46C8B806EA000B103D9AB4A64DEF17BCE4289BF4 (class_id, object_identity_id, field_name, ace_order), INDEX IDX_46C8B806EA000B103D9AB4A6DF9183C9 (class_id, object_identity_id, security_identity_id), INDEX IDX_46C8B806EA000B10 (class_id), INDEX IDX_46C8B8063D9AB4A6 (object_identity_id), INDEX IDX_46C8B806DF9183C9 (security_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_name_aliases (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_similar_names (id INT AUTO_INCREMENT NOT NULL, name1 VARCHAR(255) NOT NULL, name2 VARCHAR(255) NOT NULL, equivalence TINYINT(1) NOT NULL, UNIQUE INDEX names_unique (name1, name2), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");

        $this->addSql("ALTER TABLE acts_user_identities ADD CONSTRAINT FK_B4BCDC47A76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_user_group_links ADD CONSTRAINT FK_C789B1F6FE54D947 FOREIGN KEY (group_id) REFERENCES acts_groups (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_user_group_links ADD CONSTRAINT FK_C789B1F6A76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_object_identities ADD CONSTRAINT FK_9407E54977FA751A FOREIGN KEY (parent_object_identity_id) REFERENCES acl_object_identities (id)");
        $this->addSql("ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE2993D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE299C671CEA1 FOREIGN KEY (ancestor_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806EA000B10 FOREIGN KEY (class_id) REFERENCES acl_classes (id) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B8063D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806DF9183C9 FOREIGN KEY (security_identity_id) REFERENCES acl_security_identities (id) ON UPDATE CASCADE ON DELETE CASCADE");

        $this->addSql("ALTER TABLE `acts_events` CHANGE `socid` socid INT DEFAULT NULL");
        $this->addSql("ALTER TABLE `acts_users` ADD person_id INT DEFAULT NULL, ADD upgraded_at DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE acts_users ADD CONSTRAINT FK_62A20753217BBB47 FOREIGN KEY (person_id) REFERENCES acts_people_data (id)");
        $this->addSql("CREATE INDEX IDX_62A20753217BBB47 ON acts_users (person_id)");

        $this->addSql("ALTER TABLE acts_name_aliases ADD CONSTRAINT FK_355DA778217BBB47 FOREIGN KEY (person_id) REFERENCES acts_people_data (id)");
        $this->addSql("CREATE INDEX IDX_355DA778217BBB47 ON acts_name_aliases (person_id)");
        $this->addSql("ALTER TABLE `acts_shows` CHANGE venid venid INT DEFAULT NULL, CHANGE socid socid INT DEFAULT NULL");
        $this->addSql("UPDATE acts_shows AS s LEFT JOIN acts_venues AS v ON  s.venid = v.id SET s.venid = NULL WHERE v.id IS NULL");
        $this->addSql("UPDATE acts_shows AS s LEFT JOIN acts_societies_new AS c ON  s.socid = c.id SET s.socid = NULL WHERE c.id IS NULL");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE40A73EBA FOREIGN KEY (venid) REFERENCES acts_venues (id)");
        $this->addSql("CREATE INDEX IDX_1A1A53FE40A73EBA ON acts_shows (venid)");
        $this->addSql("ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D4E176C6");
        $this->addSql("UPDATE acts_performances AS p LEFT JOIN acts_venues AS v ON  p.venid = v.id SET p.venid = NULL WHERE v.id IS NULL");
        $this->addSql("ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D4E176C6 FOREIGN KEY (venid) REFERENCES acts_venues (id)");
        $this->addSql("UPDATE acts_events AS e LEFT JOIN acts_societies_new AS c ON  e.socid = c.id SET e.socid = NULL WHERE c.id IS NULL");
        $this->addSql("ALTER TABLE acts_events ADD CONSTRAINT FK_78452C7AAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies_new (id)");
        $this->addSql("UPDATE acts_applications AS a LEFT JOIN acts_societies_new AS c ON  a.socid = c.id SET a.socid = NULL WHERE c.id IS NULL");
        $this->addSql("ALTER TABLE acts_applications ADD CONSTRAINT FK_95ED3F0FAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies_new (id)");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FEAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies_new (id)");

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
        $this->addSql("ALTER TABLE acts_user_group_links DROP FOREIGN KEY FK_C789B1F6FE54D947");
        $this->addSql("ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806EA000B10");
        $this->addSql("ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806DF9183C9");
        $this->addSql("ALTER TABLE acl_object_identities DROP FOREIGN KEY FK_9407E54977FA751A");
        $this->addSql("ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE2993D9AB4A6");
        $this->addSql("ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE299C671CEA1");
        $this->addSql("ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B8063D9AB4A6");
        $this->addSql("DROP TABLE acts_name_aliases");
        $this->addSql("DROP TABLE acts_user_identities");
        $this->addSql("DROP TABLE acts_user_group_links");
        $this->addSql("DROP TABLE acts_groups");
        $this->addSql("DROP TABLE acl_classes");
        $this->addSql("DROP TABLE acl_security_identities");
        $this->addSql("DROP TABLE acl_object_identities");
        $this->addSql("DROP TABLE acl_object_identity_ancestors");
        $this->addSql("DROP TABLE acl_entries");
        $this->addSql("DROP TABLE acts_similar_names");
        $this->addSql("ALTER TABLE `acts_events` CHANGE `socid` socid INT NOT NULL");
        $this->addSql("DROP INDEX `IDX_1A1A53FE40A73EBA` ON `acts_shows`");
        $this->addSql("DROP INDEX `IDX_62A20753217BBB47` ON `acts_users`");
        $this->addSql("ALTER TABLE `acts_users` DROP person_id, DROP upgraded_at");
    }

}