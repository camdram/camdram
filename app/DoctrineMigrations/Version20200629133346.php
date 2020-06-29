<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200629133346 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acts_performances CHANGE sid sid INT NOT NULL');
        $this->addSql('ALTER TABLE acts_shows_people_link CHANGE sid sid INT NOT NULL');
        $this->addSql('ALTER TABLE acts_techies CHANGE showid showid INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acts_performances CHANGE sid sid INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_shows_people_link CHANGE sid sid INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_techies CHANGE showid showid INT DEFAULT NULL');
    }
}
