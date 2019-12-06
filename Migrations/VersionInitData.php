<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 20:05
 */

declare(strict_types=1);

namespace Migrations;


/**
 * Class VersionInitData
 * @package Migrations
 */
final class VersionInitData extends EDOMPAbstractMigration
{
    /**
     * @var string
     */
    protected $name = 'init_data';

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Init data migration.';
    }
}