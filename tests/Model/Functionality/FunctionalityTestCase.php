<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.6.19
 * Time: 12:01
 */

namespace Tests\Model\Functionality;

use App\Model\Functionality\BaseFunctionality;
use App\Model\Manager\ConstraintEntityManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class FunctionalityTestCase
 * @package App\AppTests\Model\Functionality
 */
abstract class FunctionalityTestCase extends TestCase
{
    /**
     * @var MockObject
     */
    protected $repositoryMock;

    /**
     * @var BaseFunctionality
     */
    protected $functionality;

    /**
     * @var MockObject
     */
    protected $em;

    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->em = $this->getMockBuilder(ConstraintEntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    abstract public function testFunctionality(): void;
}