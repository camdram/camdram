<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151013005122 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FE5FB42679');
        $this->addSql('ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FEAF648A81');
        $this->addSql('ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FEE176C6');
        $this->addSql('ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE5FB42679 FOREIGN KEY (authorizeid) REFERENCES acts_users (id)');
        $this->addSql('DROP INDEX fk_1a1a53feaf648a81 ON acts_shows');
        $this->addSql('CREATE INDEX IDX_1A1A53FEAF648A81 ON acts_shows (socid)');
        $this->addSql('DROP INDEX idx_1a1a53fe40a73eba ON acts_shows');
        $this->addSql('CREATE INDEX IDX_1A1A53FEE176C6 ON acts_shows (venid)');
        $this->addSql('DROP INDEX slugs ON acts_shows');
        $this->addSql('CREATE UNIQUE INDEX show_slugs ON acts_shows (slug)');
        $this->addSql('ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FEAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FEE176C6 FOREIGN KEY (venid) REFERENCES acts_societies (id) ON DELETE SET NULL');
        $this->addSql('DROP INDEX slugs ON acts_societies');
        $this->addSql('CREATE UNIQUE INDEX org_slugs ON acts_societies (slug)');
        $this->addSql('DROP INDEX id_2 ON acts_events');
        $this->addSql('ALTER TABLE acts_shows_people_link DROP FOREIGN KEY FK_2F5AB85E5550C4ED');
        $this->addSql('ALTER TABLE acts_shows_people_link DROP FOREIGN KEY FK_2F5AB85E57167AB4');
        $this->addSql('DROP INDEX sid ON acts_shows_people_link');
        $this->addSql('CREATE INDEX IDX_2F5AB85E57167AB4 ON acts_shows_people_link (sid)');
        $this->addSql('DROP INDEX pid ON acts_shows_people_link');
        $this->addSql('CREATE INDEX IDX_2F5AB85E5550C4ED ON acts_shows_people_link (pid)');
        $this->addSql('ALTER TABLE acts_shows_people_link ADD CONSTRAINT FK_2F5AB85E5550C4ED FOREIGN KEY (pid) REFERENCES acts_people_data (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_shows_people_link ADD CONSTRAINT FK_2F5AB85E57167AB4 FOREIGN KEY (sid) REFERENCES acts_shows (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX id ON acts_users');
        $this->addSql('DROP INDEX id ON acts_reviews');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE INDEX id_2 ON acts_events (id)');
        $this->addSql('CREATE INDEX id ON acts_reviews (id)');
        $this->addSql('ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FE5FB42679');
        $this->addSql('ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FEAF648A81');
        $this->addSql('ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FEE176C6');
        $this->addSql('ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE5FB42679 FOREIGN KEY (authorizeid) REFERENCES acts_users (id) ON DELETE SET NULL');
        $this->addSql('DROP INDEX show_slugs ON acts_shows');
        $this->addSql('CREATE UNIQUE INDEX slugs ON acts_shows (slug)');
        $this->addSql('DROP INDEX idx_1a1a53fee176c6 ON acts_shows');
        $this->addSql('CREATE INDEX IDX_1A1A53FE40A73EBA ON acts_shows (venid)');
        $this->addSql('DROP INDEX idx_1a1a53feaf648a81 ON acts_shows');
        $this->addSql('CREATE INDEX FK_1A1A53FEAF648A81 ON acts_shows (socid)');
        $this->addSql('ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FEAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FEE176C6 FOREIGN KEY (venid) REFERENCES acts_societies (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE acts_shows_people_link DROP FOREIGN KEY FK_2F5AB85E57167AB4');
        $this->addSql('ALTER TABLE acts_shows_people_link DROP FOREIGN KEY FK_2F5AB85E5550C4ED');
        $this->addSql('DROP INDEX idx_2f5ab85e5550c4ed ON acts_shows_people_link');
        $this->addSql('CREATE INDEX pid ON acts_shows_people_link (pid)');
        $this->addSql('DROP INDEX idx_2f5ab85e57167ab4 ON acts_shows_people_link');
        $this->addSql('CREATE INDEX sid ON acts_shows_people_link (sid)');
        $this->addSql('ALTER TABLE acts_shows_people_link ADD CONSTRAINT FK_2F5AB85E57167AB4 FOREIGN KEY (sid) REFERENCES acts_shows (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_shows_people_link ADD CONSTRAINT FK_2F5AB85E5550C4ED FOREIGN KEY (pid) REFERENCES acts_people_data (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX org_slugs ON acts_societies');
        $this->addSql('CREATE UNIQUE INDEX slugs ON acts_societies (slug)');
        $this->addSql('CREATE INDEX id ON acts_users (id)');
    }
}
