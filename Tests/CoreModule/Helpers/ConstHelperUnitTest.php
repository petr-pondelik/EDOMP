<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.12.19
 * Time: 16:32
 */

namespace App\Tests\CoreModule\Helpers;


use App\CoreModule\Helpers\ConstHelper;
use App\Tests\EDOMPUnitTestCase;

/**
 * Class ConstHelperUnitTest
 * @package App\Tests\CoreModule\Helpers
 */
final class ConstHelperUnitTest extends EDOMPUnitTestCase
{
    /**
     * @var ConstHelper
     */
    private $constHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->constHelper = $this->container->getByType(ConstHelper::class);
    }

    public function testValues(): void
    {
        $this->assertEquals(1, $this->constHelper::ADMIN_ROLE);
    }
}