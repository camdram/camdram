<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131227053416 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE acts_external_users ADD person_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE acts_external_users ADD CONSTRAINT FK_A75DA06C217BBB47 FOREIGN KEY (person_id) REFERENCES acts_people_data (id)");
        $this->addSql("CREATE INDEX IDX_A75DA06C217BBB47 ON acts_external_users (person_id)");
        $this->addSql("ALTER TABLE acts_shows DROP INDEX IDX_1A1A53FE53F0F7FF, ADD UNIQUE INDEX UNIQ_1A1A53FE53F0F7FF (primaryref)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE acts_external_users DROP FOREIGN KEY FK_A75DA06C217BBB47");
        $this->addSql("DROP INDEX IDX_A75DA06C217BBB47 ON acts_external_users");
        $this->addSql("ALTER TABLE acts_external_users DROP person_id");
    }
}
