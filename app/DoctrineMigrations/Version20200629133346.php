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
        $this->addSql('ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D457167AB4');
        $this->addSql('ALTER TABLE acts_shows_people_link DROP FOREIGN KEY FK_2F5AB85E57167AB4');
        $this->addSql('ALTER TABLE acts_techies DROP FOREIGN KEY FK_4D00DAC2592D0E6F');

        $this->addSql('ALTER TABLE acts_performances CHANGE sid sid INT NOT NULL');
        $this->addSql('ALTER TABLE acts_shows_people_link CHANGE sid sid INT NOT NULL');
        $this->addSql('ALTER TABLE acts_techies CHANGE showid showid INT NOT NULL');

        $this->addSql('ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D457167AB4 FOREIGN KEY (sid) REFERENCES acts_shows (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_shows_people_link ADD CONSTRAINT FK_2F5AB85E57167AB4 FOREIGN KEY (sid) REFERENCES acts_shows (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_techies ADD CONSTRAINT FK_4D00DAC2592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D457167AB4');
        $this->addSql('ALTER TABLE acts_shows_people_link DROP FOREIGN KEY FK_2F5AB85E57167AB4');
        $this->addSql('ALTER TABLE acts_techies DROP FOREIGN KEY FK_4D00DAC2592D0E6F');

        $this->addSql('ALTER TABLE acts_performances CHANGE sid sid INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_shows_people_link CHANGE sid sid INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_techies CHANGE showid showid INT DEFAULT NULL');

        $this->addSql('ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D457167AB4 FOREIGN KEY (sid) REFERENCES acts_shows (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_shows_people_link ADD CONSTRAINT FK_2F5AB85E57167AB4 FOREIGN KEY (sid) REFERENCES acts_shows (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_techies ADD CONSTRAINT FK_4D00DAC2592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id) ON DELETE CASCADE');
    }
}
