<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150330200302 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_people_data CHANGE mapto mapto INT DEFAULT NULL');
        $this->addSql('UPDATe acts_people_data p left join acts_people_data m ON m.id = p.mapto SET p.mapto = NULL WHERE m.id IS NULL');
        $this->addSql('ALTER TABLE acts_people_data ADD CONSTRAINT FK_567E1F8FE6B57CEC FOREIGN KEY (mapto) REFERENCES acts_people_data (id)');
        $this->addSql('CREATE INDEX IDX_567E1F8FE6B57CEC ON acts_people_data (mapto)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_people_data DROP FOREIGN KEY FK_567E1F8FE6B57CEC');
        $this->addSql('DROP INDEX IDX_567E1F8FE6B57CEC ON acts_people_data');
        $this->addSql('ALTER TABLE acts_people_data CHANGE mapto mapto INT NOT NULL');
    }
}
