<?php

declare(strict_types=1);

namespace Migrations;

/**
 * Class VersionTestingDataV1
 * @package Migrations
 */
final class VersionTestingDataV1 extends EDOMPAbstractMigration
{
    /**
     * @var string
     */
    protected $name = 'testing_data_v1';

    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Testing data migration V1.';
    }
}