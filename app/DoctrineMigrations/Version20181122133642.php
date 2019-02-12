<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181122133642 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //Delete all revoked access tokens
        $this->addSql('DELETE FROM acts_access WHERE revokeid IS NOT NULL');

        $this->addSql('ALTER TABLE acts_access DROP FOREIGN KEY FK_2DAB5064539B0606');
        $this->addSql('ALTER TABLE acts_access DROP FOREIGN KEY FK_2DAB50646EEF703F');
        $this->addSql('ALTER TABLE acts_access DROP FOREIGN KEY FK_2DAB5064C81B28E0');
        $this->addSql('DROP INDEX IDX_2DAB5064539B0606 ON acts_access');
        $this->addSql('DROP INDEX IDX_2DAB50646EEF703F ON acts_access');
        $this->addSql('DROP INDEX IDX_2DAB5064C81B28E0 ON acts_access');
        $this->addSql('ALTER TABLE acts_access CHANGE uid user_id INT DEFAULT NULL, CHANGE issuerid granted_by_id INT DEFAULT NULL, DROP revokeid, DROP revokedate, DROP contact, CHANGE rid entity_id INT NOT NULL, CHANGE creationdate created_at DATE NOT NULL');
        $this->addSql('ALTER TABLE acts_access ADD CONSTRAINT FK_2DAB5064A76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_access ADD CONSTRAINT FK_2DAB50643151C11F FOREIGN KEY (granted_by_id) REFERENCES acts_users (id)');
        $this->addSql('CREATE INDEX IDX_2DAB5064A76ED395 ON acts_access (user_id)');
        $this->addSql('CREATE INDEX IDX_2DAB50643151C11F ON acts_access (granted_by_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_access DROP FOREIGN KEY FK_2DAB5064A76ED395');
        $this->addSql('ALTER TABLE acts_access DROP FOREIGN KEY FK_2DAB50643151C11F');
        $this->addSql('DROP INDEX IDX_2DAB5064A76ED395 ON acts_access');
        $this->addSql('DROP INDEX IDX_2DAB50643151C11F ON acts_access');
        $this->addSql('ALTER TABLE acts_access ADD uid INT DEFAULT NULL, ADD issuerid INT DEFAULT NULL, ADD revokeid INT DEFAULT NULL, ADD revokedate DATE DEFAULT NULL, ADD contact TINYINT(1) NOT NULL, DROP user_id, DROP granted_by_id, CHANGE entity_id rid INT NOT NULL, CHANGE created_at creationdate DATE NOT NULL');
        $this->addSql('ALTER TABLE acts_access ADD CONSTRAINT FK_2DAB5064539B0606 FOREIGN KEY (uid) REFERENCES acts_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_access ADD CONSTRAINT FK_2DAB50646EEF703F FOREIGN KEY (issuerid) REFERENCES acts_users (id)');
        $this->addSql('ALTER TABLE acts_access ADD CONSTRAINT FK_2DAB5064C81B28E0 FOREIGN KEY (revokeid) REFERENCES acts_users (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_2DAB5064539B0606 ON acts_access (uid)');
        $this->addSql('CREATE INDEX IDX_2DAB50646EEF703F ON acts_access (issuerid)');
        $this->addSql('CREATE INDEX IDX_2DAB5064C81B28E0 ON acts_access (revokeid)');
    }
}
