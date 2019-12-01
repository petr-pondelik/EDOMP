<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.11.19
 * Time: 17:17
 */

namespace App\Tests\CoreModule\Model\Persistent\Functionality;


use App\CoreModule\Model\Persistent\Functionality\BaseFunctionality;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\Tests\EDOMPUnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class FunctionalityUnitTestCase
 * @package App\Tests\CoreModule\Model\Persistent\Functionality
 */
abstract class FunctionalityUnitTestCase extends EDOMPUnitTestCase
{
    /**
     * @var BaseFunctionality
     */
    protected $functionality;

    /**
     * @var MockObject
     */
    protected $em;

    /**
     * @var string
     */
    protected $dateTimeStr;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dateTimeStr = '2019-11-29 16:10:40';
        $this->em = $this->getMockBuilder(ConstraintEntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    abstract public function testCreate(): void;

    abstract public function testUpdate(): void;
}