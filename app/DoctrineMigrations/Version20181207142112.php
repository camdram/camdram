<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181207142112 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //Peformances
        $this->addSql('ALTER TABLE acts_performances ADD start_at DATETIME NOT NULL, ADD repeat_until DATE NOT NULL');
        $this->addSql('UPDATE acts_performances SET start_at = TIMESTAMP(startdate, `time`), repeat_until = enddate');
        $this->addSql('ALTER TABLE acts_performances DROP startdate, DROP enddate, DROP time');
        $this->addSql('UPDATE acts_performances SET start_at = CONVERT_TZ(start_at, "Europe/London", "UTC")');

        //Auditions
        $this->addSql('ALTER TABLE acts_auditions ADD start_at DATETIME NOT NULL, ADD end_at DATETIME DEFAULT NULL');
        $this->addSql('UPDATE acts_auditions SET start_at = TIMESTAMP(`date`, `starttime`), end_at = TIMESTAMP(`date`, `endtime`)');
        $this->addSql('ALTER TABLE acts_auditions DROP date, DROP starttime, DROP endtime');
        $this->addSql('UPDATE acts_auditions SET start_at = CONVERT_TZ(start_at, "Europe/London", "UTC"), 
            end_at = CONVERT_TZ(start_at, "Europe/London", "UTC")');
        
        //Shows
        $this->addSql('ALTER TABLE acts_shows DROP start_at, DROP end_at');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_auditions ADD date DATE NOT NULL, ADD starttime TIME NOT NULL, ADD endtime TIME DEFAULT NULL, DROP start_at, DROP end_at');
        $this->addSql('ALTER TABLE acts_performances ADD enddate DATE NOT NULL, ADD time TIME NOT NULL, DROP start_at, CHANGE repeat_until startdate DATE NOT NULL');
        $this->addSql('ALTER TABLE acts_shows ADD start_at DATETIME DEFAULT NULL, ADD end_at DATETIME DEFAULT NULL');
    }
}
