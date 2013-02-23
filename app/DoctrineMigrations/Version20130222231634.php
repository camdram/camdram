<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130222231634 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE acts_access_control_entries (id INT AUTO_INCREMENT NOT NULL, entity_id INT DEFAULT NULL, user_id INT DEFAULT NULL, group_id INT DEFAULT NULL, granted_by_id INT DEFAULT NULL, revoked_by_id INT DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, created_at DATE NOT NULL, granted_at DATE NOT NULL, revoked_at DATE DEFAULT NULL, INDEX IDX_66A875EB81257D5D (entity_id), INDEX IDX_66A875EBA76ED395 (user_id), INDEX IDX_66A875EBFE54D947 (group_id), INDEX IDX_66A875EB3151C11F (granted_by_id), INDEX IDX_66A875EBC81B28E0 (revoked_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE acts_access_control_entries ADD CONSTRAINT FK_66A875EB81257D5D FOREIGN KEY (entity_id) REFERENCES acts_entities (id)");
        $this->addSql("ALTER TABLE acts_access_control_entries ADD CONSTRAINT FK_66A875EBA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_access_control_entries ADD CONSTRAINT FK_66A875EBFE54D947 FOREIGN KEY (group_id) REFERENCES acts_groups (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_access_control_entries ADD CONSTRAINT FK_66A875EB3151C11F FOREIGN KEY (granted_by_id) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_access_control_entries ADD CONSTRAINT FK_66A875EBC81B28E0 FOREIGN KEY (revoked_by_id) REFERENCES acts_users (id) ON DELETE SET NULL");

        $this->addSql('DROP TABLE IF EXISTS `acl_entries`');
        $this->addSql('DROP TABLE IF EXISTS `acl_object_identity_ancestors`');
        $this->addSql('DROP TABLE IF EXISTS `acl_classes`');
        $this->addSql('DROP TABLE IF EXISTS `acl_object_identities`');
        $this->addSql('DROP TABLE IF EXISTS `acl_security_identities`');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE acts_access_control_entries");

    }
}
