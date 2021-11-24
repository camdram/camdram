<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211124201945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add constraint to prevent duplicate records in acts_access table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE acts_access ADD CONSTRAINT uc_entity_user_type UNIQUE (entity_id, user_id, type)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE acts_access DROP INDEX uc_entity_user_type');
    }
}
