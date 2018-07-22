<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180722122615 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('INSERT INTO `acts_performances` (sid, venid, startdate, enddate, time, venue) SELECT sid, venid, startdate, excludedate - 1, time, venue FROM `acts_performances` WHERE startdate < excludedate AND excludedate <= enddate;');
        $this->addSql('INSERT INTO `acts_performances` (sid, venid, startdate, enddate, time, venue) SELECT sid, venid, excludedate + 1, enddate, time, venue FROM `acts_performances` WHERE startdate <= excludedate AND excludedate < enddate;');
        $this->addSql('DELETE FROM `acts_performances` WHERE startdate <= excludedate AND excludedate <= enddate;');

        $this->addSql('ALTER TABLE acts_performances DROP excludedate');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_performances ADD excludedate DATE DEFAULT NULL');
    }
}
