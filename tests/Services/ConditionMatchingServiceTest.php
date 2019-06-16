<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 10.6.19
 * Time: 17:32
 */

namespace Tests\Model\Services;


use App\Helpers\ConstHelper;
use App\Helpers\StringsHelper;
use App\Services\ConditionMatchingService;
use App\Services\MathService;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;
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

    public function testResultFindConditionsMatches(): void
    {
        // EXPRESSION: $$ 15 x + 5 x + 5 = <par min="0" max="10"/> $$

        // Data for RESULT_POSITIVE condition matches find
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
                    'variableExp' => '(-5 + p0) / 20'
                ]
            ]
        ];

        // Expected matches res
        $expectedRes = [
            [ 'p0' => 6 ], [ 'p0' => 7 ], [ 'p0' => 8 ], [ 'p0' => 9 ], [ 'p0' => 10 ]
        ];

        // Get matches
        $res = $this->conditionMatchingService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // Data for RESULT_NEGATIVE condition matches find
        $data = [
            $this->constHelper::RESULT => [
                $this->constHelper::RESULT_NEGATIVE => [
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
                    'variableExp' => '(-5 + p0) / 20'
                ]
            ]
        ];

        // Expected matches res
        $expectedRes = [
            [ 'p0' => 0 ], [ 'p0' => 1 ], [ 'p0' => 2 ], [ 'p0' => 3 ], [ 'p0' => 4 ]
        ];

        // Get matches
        $res = $this->conditionMatchingService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // Data for RESULT_ZERO condition matches find
        $data = [
            $this->constHelper::RESULT => [
                $this->constHelper::RESULT_ZERO => [
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
                    'variableExp' => '(-5 + p0) / 20'
                ]
            ]
        ];

        // Expected matches res
        $expectedRes = [
            [ 'p0' => 5 ]
        ];

        // Get matches
        $res = $this->conditionMatchingService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // EXPRESSION: $$ \big(\frac{15 x + 5 x}{2}\big) + <par min="0" max="15"/> = <par min="0" max="10"/> $$

        // Data for RESULT_POSITIVE condition matches find
        $data = [
            $this->constHelper::RESULT => [
                $this->constHelper::RESULT_POSITIVE => [
                    'parametersInfo' => ArrayHash::from([
                        'count' => 2,
                        'complexity' => 176,
                        'minMax' => [
                            0 => [
                                'min' => 0,
                                'max' => 15
                            ],
                            1 => [
                                'min' => 0,
                                'max' => 10
                            ]
                        ]
                    ]),
                    'variableExp' => '(-p0 + p1) / 10'
                ]
            ]
        ];

        // Expected matches res
        $expectedRes = [
            [ 'p0' => 0, 'p1' => 1 ], [ 'p0' => 0, 'p1' => 2 ], [ 'p0' => 0, 'p1' => 3 ], [ 'p0' => 0, 'p1' => 4 ],
            [ 'p0' => 0, 'p1' => 5 ], [ 'p0' => 0, 'p1' => 6 ], [ 'p0' => 0, 'p1' => 7 ], [ 'p0' => 0, 'p1' => 8 ],
            [ 'p0' => 0, 'p1' => 9 ], [ 'p0' => 0, 'p1' => 10 ],
            [ 'p0' => 1, 'p1' => 2 ], [ 'p0' => 1, 'p1' => 3 ], [ 'p0' => 1, 'p1' => 4 ], [ 'p0' => 1, 'p1' => 5 ],
            [ 'p0' => 1, 'p1' => 6 ], [ 'p0' => 1, 'p1' => 7 ], [ 'p0' => 1, 'p1' => 8 ], [ 'p0' => 1, 'p1' => 9 ],
            [ 'p0' => 1, 'p1' => 10 ],
            [ 'p0' => 2, 'p1' => 3 ], [ 'p0' => 2, 'p1' => 4 ], [ 'p0' => 2, 'p1' => 5 ], [ 'p0' => 2, 'p1' => 6 ],
            [ 'p0' => 2, 'p1' => 7 ], [ 'p0' => 2, 'p1' => 8 ], [ 'p0' => 2, 'p1' => 9 ], [ 'p0' => 2, 'p1' => 10 ],
            [ 'p0' => 3, 'p1' => 4 ], [ 'p0' => 3, 'p1' => 5 ], [ 'p0' => 3, 'p1' => 6 ], [ 'p0' => 3, 'p1' => 7 ],
            [ 'p0' => 3, 'p1' => 8 ], [ 'p0' => 3, 'p1' => 9 ], [ 'p0' => 3, 'p1' => 10 ],
            [ 'p0' => 4, 'p1' => 5 ], [ 'p0' => 4, 'p1' => 6 ], [ 'p0' => 4, 'p1' => 7 ], [ 'p0' => 4, 'p1' => 8 ],
            [ 'p0' => 4, 'p1' => 9 ], [ 'p0' => 4, 'p1' => 10 ],
            [ 'p0' => 5, 'p1' => 6 ], [ 'p0' => 5, 'p1' => 7 ], [ 'p0' => 5, 'p1' => 8 ], [ 'p0' => 5, 'p1' => 9 ],
            [ 'p0' => 5, 'p1' => 10 ],
            [ 'p0' => 6, 'p1' => 7 ], [ 'p0' => 6, 'p1' => 8 ], [ 'p0' => 6, 'p1' => 9 ], [ 'p0' => 6, 'p1' => 10 ],
            [ 'p0' => 7, 'p1' => 8 ], [ 'p0' => 7, 'p1' => 9 ], [ 'p0' => 7, 'p1' => 10 ],
            [ 'p0' => 8, 'p1' => 9 ], [ 'p0' => 8, 'p1' => 10 ],
            [ 'p0' => 9, 'p1' => 10 ]
        ];

        // Get matches
        $res = $this->conditionMatchingService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // Data for RESULT_NEGATIVE condition matches find
        $data = [
            $this->constHelper::RESULT => [
                $this->constHelper::RESULT_NEGATIVE => [
                    'parametersInfo' => ArrayHash::from([
                        'count' => 2,
                        'complexity' => 176,
                        'minMax' => [
                            0 => [
                                'min' => 0,
                                'max' => 15
                            ],
                            1 => [
                                'min' => 0,
                                'max' => 10
                            ]
                        ]
                    ]),
                    'variableExp' => '(-p0 + p1) / 10'
                ]
            ]
        ];

        // Expected matches res
        $expectedRes = [
            [ 'p0' => 1, 'p1' => 0 ],
            [ 'p0' => 2, 'p1' => 0 ], [ 'p0' => 2, 'p1' => 1 ],
            [ 'p0' => 3, 'p1' => 0 ], [ 'p0' => 3, 'p1' => 1 ], [ 'p0' => 3, 'p1' => 2 ],
            [ 'p0' => 4, 'p1' => 0 ], [ 'p0' => 4, 'p1' => 1 ], [ 'p0' => 4, 'p1' => 2 ], [ 'p0' => 4, 'p1' => 3 ],
            [ 'p0' => 5, 'p1' => 0 ], [ 'p0' => 5, 'p1' => 1 ], [ 'p0' => 5, 'p1' => 2 ], [ 'p0' => 5, 'p1' => 3 ],
            [ 'p0' => 5, 'p1' => 4 ],
            [ 'p0' => 6, 'p1' => 0 ], [ 'p0' => 6, 'p1' => 1 ], [ 'p0' => 6, 'p1' => 2 ], [ 'p0' => 6, 'p1' => 3 ],
            [ 'p0' => 6, 'p1' => 4 ], [ 'p0' => 6, 'p1' => 5 ],
            [ 'p0' => 7, 'p1' => 0 ], [ 'p0' => 7, 'p1' => 1 ], [ 'p0' => 7, 'p1' => 2 ], [ 'p0' => 7, 'p1' => 3 ],
            [ 'p0' => 7, 'p1' => 4 ], [ 'p0' => 7, 'p1' => 5 ], [ 'p0' => 7, 'p1' => 6 ],
            [ 'p0' => 8, 'p1' => 0 ], [ 'p0' => 8, 'p1' => 1 ], [ 'p0' => 8, 'p1' => 2 ], [ 'p0' => 8, 'p1' => 3 ],
            [ 'p0' => 8, 'p1' => 4 ], [ 'p0' => 8, 'p1' => 5 ], [ 'p0' => 8, 'p1' => 6 ], [ 'p0' => 8, 'p1' => 7 ],
            [ 'p0' => 9, 'p1' => 0 ], [ 'p0' => 9, 'p1' => 1 ], [ 'p0' => 9, 'p1' => 2 ], [ 'p0' => 9, 'p1' => 3 ],
            [ 'p0' => 9, 'p1' => 4 ], [ 'p0' => 9, 'p1' => 5 ], [ 'p0' => 9, 'p1' => 6 ], [ 'p0' => 9, 'p1' => 7 ],
            [ 'p0' => 9, 'p1' => 8 ],
            [ 'p0' => 10, 'p1' => 0 ], [ 'p0' => 10, 'p1' => 1 ], [ 'p0' => 10, 'p1' => 2 ], [ 'p0' => 10, 'p1' => 3 ],
            [ 'p0' => 10, 'p1' => 4 ], [ 'p0' => 10, 'p1' => 5 ], [ 'p0' => 10, 'p1' => 6 ], [ 'p0' => 10, 'p1' => 7 ],
            [ 'p0' => 10, 'p1' => 8 ], [ 'p0' => 10, 'p1' => 9 ],
            [ 'p0' => 11, 'p1' => 0 ], [ 'p0' => 11, 'p1' => 1 ], [ 'p0' => 11, 'p1' => 2 ], [ 'p0' => 11, 'p1' => 3 ],
            [ 'p0' => 11, 'p1' => 4 ], [ 'p0' => 11, 'p1' => 5 ], [ 'p0' => 11, 'p1' => 6 ], [ 'p0' => 11, 'p1' => 7 ],
            [ 'p0' => 11, 'p1' => 8 ], [ 'p0' => 11, 'p1' => 9 ], [ 'p0' => 11, 'p1' => 10 ],
            [ 'p0' => 12, 'p1' => 0 ], [ 'p0' => 12, 'p1' => 1 ], [ 'p0' => 12, 'p1' => 2 ], [ 'p0' => 12, 'p1' => 3 ],
            [ 'p0' => 12, 'p1' => 4 ], [ 'p0' => 12, 'p1' => 5 ], [ 'p0' => 12, 'p1' => 6 ], [ 'p0' => 12, 'p1' => 7 ],
            [ 'p0' => 12, 'p1' => 8 ], [ 'p0' => 12, 'p1' => 9 ], [ 'p0' => 12, 'p1' => 10 ],
            [ 'p0' => 13, 'p1' => 0 ], [ 'p0' => 13, 'p1' => 1 ], [ 'p0' => 13, 'p1' => 2 ], [ 'p0' => 13, 'p1' => 3 ],
            [ 'p0' => 13, 'p1' => 4 ], [ 'p0' => 13, 'p1' => 5 ], [ 'p0' => 13, 'p1' => 6 ], [ 'p0' => 13, 'p1' => 7 ],
            [ 'p0' => 13, 'p1' => 8 ], [ 'p0' => 13, 'p1' => 9 ], [ 'p0' => 13, 'p1' => 10 ],
            [ 'p0' => 14, 'p1' => 0 ], [ 'p0' => 14, 'p1' => 1 ], [ 'p0' => 14, 'p1' => 2 ], [ 'p0' => 14, 'p1' => 3 ],
            [ 'p0' => 14, 'p1' => 4 ], [ 'p0' => 14, 'p1' => 5 ], [ 'p0' => 14, 'p1' => 6 ], [ 'p0' => 14, 'p1' => 7 ],
            [ 'p0' => 14, 'p1' => 8 ], [ 'p0' => 14, 'p1' => 9 ], [ 'p0' => 14, 'p1' => 10 ],
            [ 'p0' => 15, 'p1' => 0 ], [ 'p0' => 15, 'p1' => 1 ], [ 'p0' => 15, 'p1' => 2 ], [ 'p0' => 15, 'p1' => 3 ],
            [ 'p0' => 15, 'p1' => 4 ], [ 'p0' => 15, 'p1' => 5 ], [ 'p0' => 15, 'p1' => 6 ], [ 'p0' => 15, 'p1' => 7 ],
            [ 'p0' => 15, 'p1' => 8 ], [ 'p0' => 15, 'p1' => 9 ], [ 'p0' => 15, 'p1' => 10 ],
        ];

        // Get matches
        $res = $this->conditionMatchingService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // Data for RESULT_ZERO condition matches find
        $data = [
            $this->constHelper::RESULT => [
                $this->constHelper::RESULT_ZERO => [
                    'parametersInfo' => ArrayHash::from([
                        'count' => 2,
                        'complexity' => 176,
                        'minMax' => [
                            0 => [
                                'min' => 0,
                                'max' => 15
                            ],
                            1 => [
                                'min' => 0,
                                'max' => 10
                            ]
                        ]
                    ]),
                    'variableExp' => '(-p0 + p1) / 10'
                ]
            ]
        ];

        // Expected matches res
        $expectedRes = [
            [ 'p0' => 0, 'p1' => 0 ], [ 'p0' => 1, 'p1' => 1 ], [ 'p0' => 2, 'p1' => 2 ], [ 'p0' => 3, 'p1' => 3 ],
            [ 'p0' => 4, 'p1' => 4 ], [ 'p0' => 5, 'p1' => 5 ], [ 'p0' => 6, 'p1' => 6 ], [ 'p0' => 7, 'p1' => 7 ],
            [ 'p0' => 8, 'p1' => 8 ], [ 'p0' => 9, 'p1' => 9 ], [ 'p0' => 10, 'p1' => 10 ]
        ];

        // Get matches
        $res = $this->conditionMatchingService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);
    }

    public function testDiscriminantFindConditionsMatches(): void
    {
        // EXPRESSION: $$ x^2 + 4x + <par min="0" max="10"/> = 0 $$

        // Data for DISCRIMINANT_POSITIVE condition matches find
        $data = [
            $this->constHelper::DISCRIMINANT => [
                $this->constHelper::DISCRIMINANT_POSITIVE => [
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
                    'discriminantExp' => '4^2 - 4 * 1 * (p0)'
                ]
            ]
        ];

        // Expected matches res
        $expectedRes = [
            [ 'p0' => 0 ], [ 'p0' => 1 ], [ 'p0' => 2 ], [ 'p0' => 3 ]
        ];

        // Get matches
        $res = $this->conditionMatchingService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // Data for DISCRIMINANT_NEGATIVE condition matches find
        $data = [
            $this->constHelper::DISCRIMINANT => [
                $this->constHelper::DISCRIMINANT_NEGATIVE => [
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
                    'discriminantExp' => '4^2 - 4 * 1 * (p0)'
                ]
            ]
        ];

        // Expected matches res
        $expectedRes = [
            [ 'p0' => 5 ], [ 'p0' => 6 ], [ 'p0' => 7 ], [ 'p0' => 8 ], [ 'p0' => 9 ], [ 'p0' => 10 ]
        ];

        // Get matches
        $res = $this->conditionMatchingService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // Data for DISCRIMINANT_ZERO condition matches find
        $data = [
            $this->constHelper::DISCRIMINANT => [
                $this->constHelper::DISCRIMINANT_ZERO => [
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
                    'discriminantExp' => '4^2 - 4 * 1 * (p0)'
                ]
            ]
        ];

        // Expected matches res
        $expectedRes = [
            [ 'p0' => 4 ]
        ];

        // Get matches
        $res = $this->conditionMatchingService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // Data for DISCRIMINANT_INTEGER condition matches find
        $data = [
            $this->constHelper::DISCRIMINANT => [
                $this->constHelper::DISCRIMINANT_INTEGER => [
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
                    'discriminantExp' => '4^2 - 4 * 1 * (p0)'
                ]
            ]
        ];

        // Expected matches res
        $expectedRes = [
            [ 'p0' => 0 ], [ 'p0' => 1 ], [ 'p0' => 2 ], [ 'p0' => 3 ], [ 'p0' => 4 ], [ 'p0' => 5 ], [ 'p0' => 6 ],
            [ 'p0' => 7 ], [ 'p0' => 8 ], [ 'p0' => 9 ], [ 'p0' => 10 ],
        ];

        // Get matches
        $res = $this->conditionMatchingService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // Data for DISCRIMINANT_SQUARE condition matches find
        $data = [
            $this->constHelper::DISCRIMINANT => [
                $this->constHelper::DISCRIMINANT_POSITIVE_SQUARE => [
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
                    'discriminantExp' => '4^2 - 4 * 1 * (p0)'
                ]
            ]
        ];

        // Expected matches res
        $expectedRes = [
            [ 'p0' => 0 ], [ 'p0' => 3 ]
        ];

        // Get matches
        $res = $this->conditionMatchingService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);
    }
}