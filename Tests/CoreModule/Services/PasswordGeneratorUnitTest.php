<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.12.19
 * Time: 16:35
 */

namespace App\Tests\CoreModule\Services;

use App\CoreModule\Services\PasswordGenerator;
use App\Tests\EDOMPUnitTestCase;

/**
 * Class PasswordGeneratorUnitTest
 * @package App\Tests\CoreModule\Services
 */
final class PasswordGeneratorUnitTest extends EDOMPUnitTestCase
{
    /**
     * @var PasswordGenerator
     */
    protected $passwordGenerator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->passwordGenerator = $this->container->getByType(PasswordGenerator::class);
    }

    public function testGenerate(): void
    {
        $generated = $this->passwordGenerator->generate();
        $this->assertEquals(8, strlen($generated));
    }
}