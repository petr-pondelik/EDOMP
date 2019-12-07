<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.11.19
 * Time: 0:13
 */

namespace App\Tests;

use App\Bootstrap\Bootstrap;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;

/**
 * Class EDOMPTestCase
 * @package App\Tests
 */
abstract class EDOMPTestCase extends TestCase
{
    /**
     * $preserveGlobalState set to FALSE prevents PHPUnit from preserving state between tests
     * $runTestInSeparateProcess set to FALSE forces PHPUnit to run each test in separate process
     *
     * This setting solves errors caused by multiple constants definitions
     */

    /**
     * @var bool
     */
    protected $preserveGlobalState = false;

    /**
     * This disables PHPUnit diff functionality!!!
     * For show diff, comment this
     * @var bool
     */
    protected $runTestInSeparateProcess = true;

    /**
     * @var Container
     */
    protected $container;

    protected function setUp(): void
    {
        $this->container = Bootstrap::boot()->createContainer();
    }
}