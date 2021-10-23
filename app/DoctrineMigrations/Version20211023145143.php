<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211023145143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Use the Doctrine json type, not json_array';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE acts_events CHANGE socs_list socs_list LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE acts_shows CHANGE socs_list socs_list LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE acts_events CHANGE socs_list socs_list LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE acts_shows CHANGE socs_list socs_list LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json_array)\'');
    }
}
