<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 10.6.19
 * Time: 17:32
 */

namespace App\AppTests\Services;


use App\Helpers\ConstHelper;
use App\Helpers\StringsHelper;
use App\Services\ConditionMatchingService;
use App\Services\MathService;
use Nette\Utils\ArrayHash;
use NXP\MathExecutor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ConditionMatchingServiceTest
 * @package App\AppTests\Services
 */
class ConditionMatchingServiceTest extends TestCase
{
    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var MockObject
     */
    protected $mathServiceMock;

    /**
     * @var ConditionMatchingService
     */
    protected $conditionMatchingService;

    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        parent::setUp();

        // Instantiate ConstHelper class
        $this->constHelper = new ConstHelper();

        // Instantiate StringsHelper class
        $this->stringsHelper = new StringsHelper();

        // Instantiate MathExecutor
        $mathExecutor = new MathExecutor();

        // Mock the MathService
        $this->mathServiceMock = $this->getMockBuilder(MathService::class)
            ->setMethods(['evaluateExpression'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for MathService
        $this->mathServiceMock->expects($this->any())
            ->method('evaluateExpression')
            ->willReturnCallback(static function ($arg) use ($mathExecutor) {
                return $mathExecutor->execute($arg);
            });

        // Instantiate tested class
        $this->conditionMatchingService = new ConditionMatchingService($this->mathServiceMock, $this->stringsHelper, $this->constHelper);
    }

    public function testFindConditionsMatches(): void
    {
        // Data for matches find
        $data = [
            $this->constHelper::RESULT => [
                $this->constHelper::RESULT_POSITIVE => [
                    'parametersInfo' => ArrayHash::from([
                        'count' => 1,
                        'complexity' => 11,
                        'minMax' => [
                            0 => [
                                'min' => 0,
                                'max' => 10
                            ]
                        ]
                    ]),
                    'variableExp' => '(p0) / 20'
                ]
            ]
        ];

        // Expected matches res
        $expectedRes = [
            [ 'p0' => 1 ], [ 'p0' => 2 ], [ 'p0' => 3 ], [ 'p0' => 4 ], [ 'p0' => 5 ], [ 'p0' => 6 ], [ 'p0' => 7 ],
            [ 'p0' => 8 ], [ 'p0' => 9 ], [ 'p0' => 10 ]
        ];

        // Get matches
        $res = $this->conditionMatchingService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);
    }
}