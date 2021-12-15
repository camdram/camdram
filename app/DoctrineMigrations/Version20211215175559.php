<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211215175559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add constraint to prevent duplicate records in acts_access table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX idx_entity_type_user ON acts_access (entity_id, type, user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_entity_type_user ON acts_access');
    }
}
