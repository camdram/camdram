<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181203000730 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // New table and column
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE acts_show_soc_link (show_id INT NOT NULL, society_id INT NOT NULL, INDEX IDX_DCC3C2ED0C1FC64 (show_id), INDEX IDX_DCC3C2EE6389D24 (society_id), PRIMARY KEY(show_id, society_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acts_show_soc_link ADD CONSTRAINT FK_DCC3C2ED0C1FC64 FOREIGN KEY (show_id) REFERENCES acts_shows (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_show_soc_link ADD CONSTRAINT FK_DCC3C2EE6389D24 FOREIGN KEY (society_id) REFERENCES acts_societies (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FEAF648A81');
        $this->addSql('DROP INDEX IDX_1A1A53FEAF648A81 ON acts_shows');
        $this->addSql('ALTER TABLE acts_shows ADD socs_list TEXT NOT NULL');

        // Migrate
        $this->addSql('UPDATE acts_shows SET socs_list = "[]"');
        $this->addSql('UPDATE acts_shows SET socs_list = CONCAT("[", JSON_QUOTE(society), "]") ' .
                      'WHERE socid IS NULL AND COALESCE(society, "") <> "";');
        $this->addSql('UPDATE acts_shows SET socs_list = CONCAT("[", socid, "]") WHERE socid IS NOT NULL;');
        $this->addSql('INSERT INTO acts_show_soc_link (show_id, society_id) SELECT id, socid FROM acts_shows WHERE socid IS NOT NULL');

        // Drop old columns
        $this->addSql('ALTER TABLE acts_shows DROP socid, DROP society');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE acts_show_soc_link');
        $this->addSql('ALTER TABLE acts_shows ADD socid INT DEFAULT NULL, ADD society VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, DROP socs_list');
        $this->addSql('ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FEAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_1A1A53FEAF648A81 ON acts_shows (socid)');
    }
}
