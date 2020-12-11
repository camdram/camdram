<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201211004819 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE acts_advert_position_link (advert_id INT NOT NULL, position_id INT NOT NULL, INDEX IDX_E7FD3A3BD07ECCB6 (advert_id), INDEX IDX_E7FD3A3BDD842E46 (position_id), PRIMARY KEY(advert_id, position_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_position_tags (id INT AUTO_INCREMENT NOT NULL, position_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_514471ACDD842E46 (position_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acts_advert_position_link ADD CONSTRAINT FK_E7FD3A3BD07ECCB6 FOREIGN KEY (advert_id) REFERENCES acts_adverts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_advert_position_link ADD CONSTRAINT FK_E7FD3A3BDD842E46 FOREIGN KEY (position_id) REFERENCES acts_positions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_position_tags ADD CONSTRAINT FK_514471ACDD842E46 FOREIGN KEY (position_id) REFERENCES acts_positions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_shows_people_link ADD position_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_shows_people_link ADD CONSTRAINT FK_2F5AB85EDD842E46 FOREIGN KEY (position_id) REFERENCES acts_positions (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_2F5AB85EDD842E46 ON acts_shows_people_link (position_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE acts_advert_position_link');
        $this->addSql('DROP TABLE acts_position_tags');
        $this->addSql('ALTER TABLE acts_shows_people_link DROP FOREIGN KEY FK_2F5AB85EDD842E46');
        $this->addSql('DROP INDEX IDX_2F5AB85EDD842E46 ON acts_shows_people_link');
        $this->addSql('ALTER TABLE acts_shows_people_link DROP position_id');
    }
}
