<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201114115949 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Organisation contact email';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acts_societies ADD contact_email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_venues ADD contact_email VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acts_societies DROP contact_email');
        $this->addSql('ALTER TABLE acts_venues DROP contact_email');
    }
}
