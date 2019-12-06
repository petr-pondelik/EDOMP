<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 20:47
 */

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class EDOMPAbstractMigration
 * @package Migrations
 */
abstract class EDOMPAbstractMigration extends AbstractMigration
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @return string
     */
    public function getScriptsDir(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'scripts';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql(file_get_contents($this->getScriptsDir() . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . 'up.sql'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql(file_get_contents($this->getScriptsDir() . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . 'down.sql'));
    }
}