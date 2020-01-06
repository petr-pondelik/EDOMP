<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Nette\Utils\FileSystem;

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

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        parent::up($schema);

        for ($i = 1; $i < 4; $i++) {
            FileSystem::copy(
                __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'logos'. DIRECTORY_SEPARATOR . 'file.jpg',
                __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'data_public' . DIRECTORY_SEPARATOR . 'logos' . DIRECTORY_SEPARATOR . $i . DIRECTORY_SEPARATOR . 'file.jpg'
            );
        }

        for ($i = 1; $i < 7; $i++) {
            FileSystem::copy(
                __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $i,
                __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $i
            );
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        parent::down($schema);

        for ($i = 1; $i < 4; $i++) {
            FileSystem::delete(
                __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'data_public' . DIRECTORY_SEPARATOR . 'logos' . DIRECTORY_SEPARATOR . $i
            );
        }

        for ($i = 1; $i < 7; $i++) {
            FileSystem::delete(
                __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $i
            );
        }
    }
}