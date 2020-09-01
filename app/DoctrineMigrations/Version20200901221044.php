<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200901221044 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acts_events ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_events ADD CONSTRAINT FK_78452C7A3DA5256D FOREIGN KEY (image_id) REFERENCES images (id)');
        $this->addSql('CREATE INDEX IDX_78452C7A3DA5256D ON acts_events (image_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acts_events DROP FOREIGN KEY FK_78452C7A3DA5256D');
        $this->addSql('DROP INDEX IDX_78452C7A3DA5256D ON acts_events');
        $this->addSql('ALTER TABLE acts_events DROP image_id');
    }
}
