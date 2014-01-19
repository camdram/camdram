<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140111200737 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FE40A73EBA");
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FEAF648A81");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FEE176C6 FOREIGN KEY (venid) REFERENCES acts_societies (id) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FEAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id) ON DELETE SET NULL");
        $this->addSql("CREATE INDEX IDX_A6F619725B919408 ON acts_support (supportid)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_6FB69A2F5F37A13B ON acts_api_auth_codes (token)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_E452518D5F37A13B ON acts_api_access_tokens (token)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_CACE640E5F37A13B ON acts_api_refresh_tokens (token)");
        $this->addSql("ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D4E176C6");
        $this->addSql("ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D4E176C6 FOREIGN KEY (venid) REFERENCES acts_societies (id) ON DELETE SET NULL");

        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FE5FB42679");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE5FB42679 FOREIGN KEY (authorizeid) REFERENCES acts_users (id) ON DELETE SET NULL");

        $this->addSql("ALTER TABLE acts_shows_people_link DROP FOREIGN KEY FK_2F5AB85E5550C4ED");
        $this->addSql("ALTER TABLE acts_shows_people_link DROP FOREIGN KEY FK_2F5AB85E57167AB4");
        $this->addSql("ALTER TABLE acts_shows_people_link ADD CONSTRAINT FK_2F5AB85E5550C4ED FOREIGN KEY (pid) REFERENCES acts_people_data (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_shows_people_link ADD CONSTRAINT FK_2F5AB85E57167AB4 FOREIGN KEY (sid) REFERENCES acts_shows (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_applications DROP FOREIGN KEY FK_95ED3F0FAF648A81");
        $this->addSql("ALTER TABLE acts_applications ADD CONSTRAINT FK_95ED3F0FAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_events DROP FOREIGN KEY FK_78452C7AAF648A81");
        $this->addSql("ALTER TABLE acts_events ADD CONSTRAINT FK_78452C7AAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id) ON DELETE SET NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE acts_events DROP FOREIGN KEY FK_78452C7AAF648A81");
        $this->addSql("ALTER TABLE acts_events ADD CONSTRAINT FK_78452C7AAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id)");
        $this->addSql("ALTER TABLE acts_applications DROP FOREIGN KEY FK_95ED3F0FAF648A81");
        $this->addSql("ALTER TABLE acts_applications ADD CONSTRAINT FK_95ED3F0FAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id)");
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FE5FB42679");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE5FB42679 FOREIGN KEY (authorizeid) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_shows_people_link DROP FOREIGN KEY FK_2F5AB85E57167AB4");
        $this->addSql("ALTER TABLE acts_shows_people_link DROP FOREIGN KEY FK_2F5AB85E5550C4ED");
        $this->addSql("ALTER TABLE acts_shows_people_link ADD CONSTRAINT FK_2F5AB85E57167AB4 FOREIGN KEY (sid) REFERENCES acts_shows (id)");
        $this->addSql("ALTER TABLE acts_shows_people_link ADD CONSTRAINT FK_2F5AB85E5550C4ED FOREIGN KEY (pid) REFERENCES acts_people_data (id)");
        $this->addSql("ALTER TABLE acts_support DROP FOREIGN KEY FK_A6F619725B919408");
        $this->addSql("ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D4E176C6");
        $this->addSql("ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D4E176C6 FOREIGN KEY (venid) REFERENCES acts_societies (id)");
        $this->addSql("DROP INDEX UNIQ_E452518D5F37A13B ON acts_api_access_tokens");
        $this->addSql("DROP INDEX UNIQ_6FB69A2F5F37A13B ON acts_api_auth_codes");
        $this->addSql("DROP INDEX UNIQ_CACE640E5F37A13B ON acts_api_refresh_tokens");
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FEE176C6");
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FEAF648A81");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE40A73EBA FOREIGN KEY (venid) REFERENCES acts_societies (id)");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FEAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id)");
    }
}
