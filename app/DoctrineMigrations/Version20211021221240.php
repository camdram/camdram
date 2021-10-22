<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211021221240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add content warnings to shows.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE acts_shows ADD content_warning LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE acts_shows DROP content_warning');
    }
}
