<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class VersionTestingDataV1
 * @package Migrations
 */
final class VersionTestingDataV1 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Testing data migration V1.';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'testing_data_v1' . DIRECTORY_SEPARATOR . 'up.sql'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'testing_data_v1' . DIRECTORY_SEPARATOR . 'down.sql'));
    }
}
