<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200518103009 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX idx_person_fulltext ON acts_people_data');
        $this->addSql('CREATE FULLTEXT INDEX idx_person_fulltext ON acts_people_data (name, slug)');
        $this->addSql('DROP INDEX idx_show_fulltext ON acts_shows');
        $this->addSql('CREATE FULLTEXT INDEX idx_show_fulltext ON acts_shows (title, slug)');
        $this->addSql('DROP INDEX idx_society_fulltext ON acts_societies');
        $this->addSql('CREATE FULLTEXT INDEX idx_society_fulltext ON acts_societies (name, shortname, slug)');
        $this->addSql('DROP INDEX idx_venue_fulltext ON acts_venues');
        $this->addSql('CREATE FULLTEXT INDEX idx_venue_fulltext ON acts_venues (name, shortname, slug)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX idx_person_fulltext ON acts_people_data');
        $this->addSql('CREATE FULLTEXT INDEX idx_person_fulltext ON acts_people_data (name)');
        $this->addSql('DROP INDEX idx_show_fulltext ON acts_shows');
        $this->addSql('CREATE FULLTEXT INDEX idx_show_fulltext ON acts_shows (title)');
        $this->addSql('DROP INDEX idx_society_fulltext ON acts_societies');
        $this->addSql('CREATE FULLTEXT INDEX idx_society_fulltext ON acts_societies (name, shortname)');
        $this->addSql('DROP INDEX idx_venue_fulltext ON acts_venues');
        $this->addSql('CREATE FULLTEXT INDEX idx_venue_fulltext ON acts_venues (name, shortname)');
    }
}
