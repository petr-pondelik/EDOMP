<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.12.19
 * Time: 17:15
 */

namespace App\Tests\TeacherModule\Services;

use App\CoreModule\Helpers\ConstHelper;
use App\TeacherModule\Model\NonPersistent\Entity\ArithmeticSequenceTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\GeometricSequenceTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\LinearEquationTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\QuadraticEquationTemplateNP;
use App\TeacherModule\Model\NonPersistent\TemplateData\ParametersData;
use App\TeacherModule\Services\ConditionService;
use App\Tests\EDOMPTestCase;
use Nette\Utils\ArrayHash;

/**
 * Class ConditionServiceIntegrationTest
 * @package App\Tests\TeacherModule\Services
 */
final class ConditionServiceIntegrationTest extends EDOMPTestCase
{
    /**
     * @var ConditionService
     */
    protected $conditionService;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->conditionService = $this->container->getByType(ConditionService::class);
        $this->constHelper = $this->container->getByType(ConstHelper::class);
    }

    public function testLinearEquationConditionMatches(): void
    {
        // EXPRESSION: $$ 15 x + 5 x + 5 = \frac{<par min="-2" max="4"/>}{<par min="-2" max="4"/>} $$

        $values = ArrayHash::from([
            'type' => 1,
            'subTheme' => 1,
            'studentVisible' => 1,
            'textBefore' => '',
            'body' => '$$ 15 x + 5 x + 5 = \frac{<par min="-2" max="4"/>}{<par min="-2" max="4"/>} $$',
            'textAfter' => '',
            'difficulty' => 1,
            'condition_1' => 0,
            'variable' => 'x',
            'expression' => '15 x + 5 x + 5 = (( p0 )/( p1 ))',
            'linearVariableExpression' => '(-5 + p0 / p1)/(20)',
            'standardized' => '20 x + 5 - p0 / p1'
        ]);

        $parametersData = new ParametersData([
            'count' => 2,
            'complexity' => 49,
            'minMax' => [
                '0' => ['min' => -2, 'max' => 4],
                '1' => ['min' => -2, 'max' => 4]
            ]
        ]);

        $linearEquationTemplateNP = new LinearEquationTemplateNP($values);
        $linearEquationTemplateNP->setParametersData($parametersData);

        // Data for RESULT_POSITIVE condition matches find
        $data = [
            $this->constHelper::LINEAR_EQUATION_VALIDATION => [
                $this->constHelper::LINEAR_EQUATION_VALID => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 5; $i++) {
            for ($j = -2; $j < 5; $j++) {
                if ($j !== 0) {
                    $expectedRes[] = ['p0' => $i, 'p1' => $j];
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // =============================================================================================================

        // EXPRESSION: $$ <par min="-2" max="3"/> x + <par min="-2" max="3"/> x + 5 = 2 $$

        $values = ArrayHash::from([
            'type' => 1,
            'subTheme' => 1,
            'studentVisible' => 1,
            'textBefore' => '',
            'body' => '$$ <par min="-2" max="3"/> x + <par min="-2" max="3"/> x + 5 = 2 $$',
            'textAfter' => '',
            'difficulty' => 1,
            'condition_1' => 0,
            'variable' => 'x',
            'expression' => 'p0 x + p1 x + 5 = 2',
            'linearVariableExpression' => '(-3)/(p0 + p1)',
            'standardized' => '(p0 + p1) x + 3',
        ]);

        $parametersData = new ParametersData([
            'count' => 2,
            'complexity' => 36,
            'minMax' => [
                '0' => ['min' => -2, 'max' => 3],
                '1' => ['min' => -2, 'max' => 3]
            ]
        ]);

        $linearEquationTemplateNP = new LinearEquationTemplateNP($values);
        $linearEquationTemplateNP->setParametersData($parametersData);

        // Data for RESULT_POSITIVE condition matches find
        $data = [
            $this->constHelper::LINEAR_EQUATION_VALIDATION => [
                $this->constHelper::LINEAR_EQUATION_VALID => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 4; $i++) {
            for ($j = -2; $j < 4; $j++) {
                // If $i + $j is equal to zero, the result won't be linear equation
                if ($i + $j) {
                    $expectedRes[] = ['p0' => $i, 'p1' => $j];
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // =============================================================================================================

        // EXPRESSION: $$ <par min="1" max="3"/> x = <par min="1" max="2"/> x + 2 $$

        $values = ArrayHash::from([
            'type' => 1,
            'subTheme' => 1,
            'studentVisible' => 1,
            'textBefore' => '',
            'body' => '$$ <par min="1" max="3"/> x = <par min="1" max="2"/> x + 2 $$',
            'textAfter' => '',
            'difficulty' => 1,
            'condition_1' => 0,
            'variable' => 'x',
            'expression' => 'p0 x = p1 x + 2',
            'linearVariableExpression' => '(2)/((p0 - p1))',
            'standardized' => '(p0 - p1) x - 2',
        ]);

        $parametersData = new ParametersData([
            'count' => 2,
            'complexity' => 6,
            'minMax' => [
                '0' => ['min' => 1, 'max' => 3],
                '1' => ['min' => 1, 'max' => 3]
            ]
        ]);

        $linearEquationTemplateNP = new LinearEquationTemplateNP($values);
        $linearEquationTemplateNP->setParametersData($parametersData);

        // Data for RESULT_POSITIVE condition matches find
        $data = [
            $this->constHelper::LINEAR_EQUATION_VALIDATION => [
                $this->constHelper::LINEAR_EQUATION_VALID => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = 1; $i < 4; $i++) {
            for ($j = 1; $j < 4; $j++) {
                // If $i - $j is equal to zero, the result won't be linear equation
                if ($i - $j) {
                    $expectedRes[] = ['p0' => $i, 'p1' => $j];
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);
    }

    public function testResultConditionMatches(): void
    {
        // EXPRESSION: $$ 15 x + 5 x + 5 = \frac{<par min="-2" max="4"/>}{<par min="-2" max="4"/>} $$

        $values = ArrayHash::from([
            'type' => 1,
            'subTheme' => 1,
            'studentVisible' => 1,
            'textBefore' => '',
            'body' => '$$ 15 x + 5 x + 5 = \frac{<par min="-2" max="4"/>}{<par min="-2" max="4"/>} $$',
            'textAfter' => '',
            'difficulty' => 1,
            'condition_1' => 0,
            'variable' => 'x',
            'expression' => '15 x + 5 x + 5 = (( p0 )/( p1 ))',
            'linearVariableExpression' => '(-5 + p0 / p1)/(20)',
            'standardized' => '20 x + 5 - p0 / p1',
            'conditionValidateItem' => 'linearVariableExpression'
        ]);

        $parametersData = new ParametersData([
            'count' => 2,
            'complexity' => 49,
            'minMax' => [
                '0' => ['min' => -2, 'max' => 4],
                '1' => ['min' => -2, 'max' => 4]
            ]
        ]);

        $linearEquationTemplateNP = new LinearEquationTemplateNP($values);
        $linearEquationTemplateNP->setParametersData($parametersData);

        // Data for RESULT_POSITIVE condition matches find
        $data = [
            $this->constHelper::RESULT => [
                $this->constHelper::RESULT_POSITIVE => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = null;

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);


        // Data for RESULT_ZERO condition matches find
        $data = [
            $this->constHelper::RESULT => [
                $this->constHelper::RESULT_ZERO => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = null;

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);


        // Data for RESULT_NEGATIVE condition matches find
        $data = [
            $this->constHelper::RESULT => [
                $this->constHelper::RESULT_NEGATIVE => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 5; $i++) {
            for ($j = -2; $j < 5; $j++) {
                if ($j !== 0) {
                    $expectedRes[] = ['p0' => $i, 'p1' => $j];
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // =============================================================================================================

        // EXPRESSION: $$ <par min="-2" max="3"/> x + <par min="-2" max="3"/> x + 5 = 2 $$

        $values = ArrayHash::from([
            'type' => 1,
            'subTheme' => 1,
            'studentVisible' => 1,
            'textBefore' => '',
            'body' => '$$ <par min="-2" max="3"/> x + <par min="-2" max="3"/> x + 5 = 2 $$',
            'textAfter' => '',
            'difficulty' => 1,
            'condition_1' => 0,
            'variable' => 'x',
            'expression' => 'p0 x + p1 x + 5 = 2',
            'linearVariableExpression' => '(-3)/(p0 + p1)',
            'standardized' => '(p0 + p1) x + 3',
            'conditionValidateItem' => 'linearVariableExpression'
        ]);

        $parametersData = new ParametersData([
            'count' => 2,
            'complexity' => 36,
            'minMax' => [
                '0' => ['min' => -2, 'max' => 3],
                '1' => ['min' => -2, 'max' => 3]
            ]
        ]);

        $linearEquationTemplateNP = new LinearEquationTemplateNP($values);
        $linearEquationTemplateNP->setParametersData($parametersData);

        // Data for RESULT_POSITIVE condition matches find
        $data = [
            $this->constHelper::RESULT => [
                $this->constHelper::RESULT_POSITIVE => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 5; $i++) {
            for ($j = -2; $j < 5; $j++) {
                if ($i + $j < 0) {
                    $expectedRes[] = ['p0' => $i, 'p1' => $j];
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);


        // Data for RESULT_ZERO condition matches find
        $data = [
            $this->constHelper::RESULT => [
                $this->constHelper::RESULT_ZERO => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = null;

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);


        // Data for RESULT_NEGATIVE condition matches find
        $data = [
            $this->constHelper::RESULT => [
                $this->constHelper::RESULT_NEGATIVE => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 4; $i++) {
            for ($j = -2; $j < 4; $j++) {
                if ($i + $j > 0) {
                    $expectedRes[] = ['p0' => $i, 'p1' => $j];
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // =============================================================================================================

        // EXPRESSION: $$ <par min="-2" max="3"/> x = <par min="-2" max="2"/> x + 2 $$

        $values = ArrayHash::from([
            'type' => 1,
            'subTheme' => 1,
            'studentVisible' => 1,
            'textBefore' => '',
            'body' => '$$ <par min="-2" max="3"/> x = <par min="-2" max="2"/> x + 2 $$',
            'textAfter' => '',
            'difficulty' => 1,
            'condition_1' => 0,
            'variable' => 'x',
            'expression' => 'p0 x = p1 x + 2',
            'linearVariableExpression' => '(2)/((p0 - p1))',
            'standardized' => '(p0 - p1) x - 2',
            'conditionValidateItem' => 'linearVariableExpression'
        ]);

        $parametersData = new ParametersData([
            'count' => 2,
            'complexity' => 6,
            'minMax' => [
                '0' => ['min' => -2, 'max' => 3],
                '1' => ['min' => -2, 'max' => 2]
            ]
        ]);

        $linearEquationTemplateNP = new LinearEquationTemplateNP($values);
        $linearEquationTemplateNP->setParametersData($parametersData);

        // Data for RESULT_POSITIVE condition matches find
        $data = [
            $this->constHelper::RESULT => [
                $this->constHelper::RESULT_POSITIVE => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 4; $i++) {
            for ($j = -2; $j < 3; $j++) {
                // If $i - $j is equal to zero, the result won't be linear equation
                if ($i - $j > 0) {
                    $expectedRes[] = ['p0' => $i, 'p1' => $j];
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);


        // Data for RESULT_POSITIVE condition matches find
        $data = [
            $this->constHelper::RESULT => [
                $this->constHelper::RESULT_ZERO => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = null;

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);


        // Data for RESULT_POSITIVE condition matches find
        $data = [
            $this->constHelper::RESULT => [
                $this->constHelper::RESULT_NEGATIVE => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 4; $i++) {
            for ($j = -2; $j < 3; $j++) {
                // If $i - $j is equal to zero, the result won't be linear equation
                if ($i - $j < 0) {
                    $expectedRes[] = ['p0' => $i, 'p1' => $j];
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);
    }

    public function testQuadraticEquationConditionMatches(): void
    {
        // EXPRESSION: $$ <par min="-2" max="2"/> x^2 + <par min="-2" max="2"/>x + <par min="-2" max="2"/> = 0 $$

        $values = ArrayHash::from([
            'type' => 2,
            'subTheme' => 2,
            'studentVisible' => 1,
            'textBefore' => '',
            'body' => '$$ <par min="-2" max="2"/> x^2 + <par min="-2" max="2"/>x + <par min="-2" max="2"/> = 0 $$',
            'textAfter' => '',
            'difficulty' => 1,
            'condition_1' => 0,
            'variable' => 'x',
            'expression' => 'p0 x^2 + p1 x + p2 = 0',
            'standardized' => 'p0 x^2 + p1 x + p2',
            'discriminant' => null
        ]);

        $parametersData = new ParametersData([
            'count' => 3,
            'complexity' => 125,
            'minMax' => [
                '0' => ['min' => -2, 'max' => 2],
                '1' => ['min' => -2, 'max' => 2],
                '2' => ['min' => -2, 'max' => 2]
            ]
        ]);

        $linearEquationTemplateNP = new QuadraticEquationTemplateNP($values);
        $linearEquationTemplateNP->setParametersData($parametersData);

        // Data for QUADRATIC_EQUATION condition matches find
        $data = [
            $this->constHelper::QUADRATIC_EQUATION_VALIDATION => [
                $this->constHelper::IS_QUADRATIC_EQUATION => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 3; $i++) {
            for ($j = -2; $j < 3; $j++) {
                for ($k = -2; $k < 3; $k++) {
                    if ($i !== 0) {
                        $expectedRes[] = ['p0' => $i, 'p1' => $j, 'p2' => $k];
                    }
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // =============================================================================================================

        // EXPRESSION: $$ <par min="-2" max="2"/> x^2 + 5 = <par min="-2" max="2"/> x^2 $$

        $values = ArrayHash::from([
            'type' => 2,
            'subTheme' => 2,
            'studentVisible' => 1,
            'textBefore' => '',
            'body' => '$$ <par min="-2" max="2"/> x^2 + 5 = <par min="-2" max="2"/> x^2 $$',
            'textAfter' => '',
            'difficulty' => 1,
            'condition_1' => 0,
            'variable' => 'x',
            'expression' => 'p0 x^2 + 5 = p1 x^2',
            'standardized' => '(p0 - p1) x^2 + 5',
            'discriminant' => null
        ]);

        $parametersData = new ParametersData([
            'count' => 2,
            'complexity' => 25,
            'minMax' => [
                '0' => ['min' => -2, 'max' => 2],
                '1' => ['min' => -2, 'max' => 2],
            ]
        ]);

        $linearEquationTemplateNP = new QuadraticEquationTemplateNP($values);
        $linearEquationTemplateNP->setParametersData($parametersData);

        // Data for QUADRATIC_EQUATION condition matches find
        $data = [
            $this->constHelper::QUADRATIC_EQUATION_VALIDATION => [
                $this->constHelper::IS_QUADRATIC_EQUATION => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 3; $i++) {
            for ($j = -2; $j < 3; $j++) {
                if ($i - $j) {
                    $expectedRes[] = ['p0' => $i, 'p1' => $j];
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // =============================================================================================================

        // EXPRESSION: $$ <par min="-2" max="2"/> x^2 + 5 = <par min="-2" max="2"/> x^2 $$

        $values = ArrayHash::from([
            'type' => 2,
            'subTheme' => 2,
            'studentVisible' => 1,
            'textBefore' => '',
            'body' => '$$ \frac{<par min="-2" max="2"/> x - 1}{x} + \frac{x}{x - 1} + \frac{<par min="-2" max="2"/>}{x - 1} = 0 $$',
            'textAfter' => '',
            'difficulty' => 1,
            'condition_1' => 0,
            'variable' => 'x',
            'expression' => '(( p0  x - 1)/(x)) + ((x)/(x - 1)) + (( p1 )/(x - 1)) = 0',
            'standardized' => '(1 + p0) x^2 + (-1 - p0 + p1) x + 1',
            'discriminant' => null
        ]);

        $parametersData = new ParametersData([
            'count' => 2,
            'complexity' => 25,
            'minMax' => [
                '0' => ['min' => -2, 'max' => 2],
                '1' => ['min' => -2, 'max' => 2],
            ]
        ]);

        $linearEquationTemplateNP = new QuadraticEquationTemplateNP($values);
        $linearEquationTemplateNP->setParametersData($parametersData);

        // Data for QUADRATIC_EQUATION condition matches find
        $data = [
            $this->constHelper::QUADRATIC_EQUATION_VALIDATION => [
                $this->constHelper::IS_QUADRATIC_EQUATION => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 3; $i++) {
            for ($j = -2; $j < 3; $j++) {
                if ($i !== -1) {
                    $expectedRes[] = ['p0' => $i, 'p1' => $j];
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);
    }

    public function testDiscriminantConditionMatches(): void
    {
        // EXPRESSION: $$ <par min="-2" max="2"/> x^2 + <par min="-2" max="2"/>x + <par min="-2" max="2"/> = 0 $$

        $values = ArrayHash::from([
            'type' => 2,
            'subTheme' => 2,
            'studentVisible' => 1,
            'textBefore' => '',
            'body' => '$$ <par min="-2" max="2"/> x^2 + <par min="-2" max="2"/>x + <par min="-2" max="2"/> = 0 $$',
            'textAfter' => '',
            'difficulty' => 1,
            'condition_1' => 0,
            'variable' => 'x',
            'expression' => 'p0 x^2 + p1 x + p2 = 0',
            'standardized' => 'p0 x^2 + p1 x + p2',
            'discriminant' => '(p1)^2 - 4 * (p0) * (p2)',
            'conditionValidateItem' => 'discriminant'
        ]);

        $parametersData = new ParametersData([
            'count' => 3,
            'complexity' => 125,
            'minMax' => [
                '0' => ['min' => -2, 'max' => 2],
                '1' => ['min' => -2, 'max' => 2],
                '2' => ['min' => -2, 'max' => 2]
            ]
        ]);

        $linearEquationTemplateNP = new QuadraticEquationTemplateNP($values);
        $linearEquationTemplateNP->setParametersData($parametersData);

        // Data for DISCRIMINANT_POSITIVE condition matches find
        $data = [
            $this->constHelper::DISCRIMINANT => [
                $this->constHelper::DISCRIMINANT_POSITIVE => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 3; $i++) {
            for ($j = -2; $j < 3; $j++) {
                for ($k = -2; $k < 3; $k++) {
                    if (($j ** 2) > 4 * $i * $k) {
                        $expectedRes[] = ['p0' => $i, 'p1' => $j, 'p2' => $k];
                    }
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);


        // Data for DISCRIMINANT_ZERO condition matches find
        $data = [
            $this->constHelper::DISCRIMINANT => [
                $this->constHelper::DISCRIMINANT_ZERO => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 3; $i++) {
            for ($j = -2; $j < 3; $j++) {
                for ($k = -2; $k < 3; $k++) {
                    if (($j ** 2) === 4 * $i * $k) {
                        $expectedRes[] = ['p0' => $i, 'p1' => $j, 'p2' => $k];
                    }
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);


        // Data for DISCRIMINANT_NEGATIVE condition matches find
        $data = [
            $this->constHelper::DISCRIMINANT => [
                $this->constHelper::DISCRIMINANT_NEGATIVE => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 3; $i++) {
            for ($j = -2; $j < 3; $j++) {
                for ($k = -2; $k < 3; $k++) {
                    if (($j ** 2) < 4 * $i * $k) {
                        $expectedRes[] = ['p0' => $i, 'p1' => $j, 'p2' => $k];
                    }
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);


        // Data for DISCRIMINANT_NEGATIVE condition matches find
        $data = [
            $this->constHelper::DISCRIMINANT => [
                $this->constHelper::DISCRIMINANT_INTEGER => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 3; $i++) {
            for ($j = -2; $j < 3; $j++) {
                for ($k = -2; $k < 3; $k++) {
                    $discriminant = ($j ** 2) - 4 * $i * $k;
                    if (is_int($discriminant)) {
                        $expectedRes[] = ['p0' => $i, 'p1' => $j, 'p2' => $k];
                    }
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // Data for DISCRIMINANT_NEGATIVE condition matches find
        $data = [
            $this->constHelper::DISCRIMINANT => [
                $this->constHelper::DISCRIMINANT_POSITIVE_SQUARE => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 3; $i++) {
            for ($j = -2; $j < 3; $j++) {
                for ($k = -2; $k < 3; $k++) {
                    $discriminant = ($j ** 2) - 4 * $i * $k;
                    if ($discriminant > 0) {
                        $squareRoot = sqrt($discriminant);
                        $squareRootInt = (int)$squareRoot;
                        if ($squareRoot == $squareRootInt) {
                            $expectedRes[] = ['p0' => $i, 'p1' => $j, 'p2' => $k];
                        }
                    }
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);
    }

    public function testArithmeticSequenceConditionMatches(): void
    {
        // EXPRESSION: $$ a_n = <par min="-2" max="2"/> - <par min="-2" max="2"/> n $$

        $values = [
            'type' => 3,
            'firstN' => 5,
            'firstValues' => [
                'p0 - 1*p1',
                'p0 - 2*p1',
                'p0 - 3*p1'
            ],
            'subTheme' => 3,
            'studentVisible' => 1,
            'textBefore' => '',
            'body' => '$$ a_n = <par min="-2" max="2"/> - <par min="-2" max="2"/> n $$',
            'textAfter' => '',
            'difficulty' => 1,
            'condition_1' => 0,
            'variable' => 'x',
            'expression' => 'an = p0 - p1 n',
            'standardized' => 'p0 - n p1',
        ];

        $parametersData = new ParametersData([
            'count' => 2,
            'complexity' => 25,
            'minMax' => [
                '0' => ['min' => -2, 'max' => 2],
                '1' => ['min' => -2, 'max' => 2],
            ]
        ]);

        $linearEquationTemplateNP = new ArithmeticSequenceTemplateNP($values);
        $linearEquationTemplateNP->setParametersData($parametersData);

        // Data for QUADRATIC_EQUATION condition matches find
        $data = [
            $this->constHelper::DIFFERENCE_VALIDATION => [
                $this->constHelper::DIFFERENCE_EXISTS => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 3; $i++) {
            for ($j = -2; $j < 3; $j++) {
                $expectedRes[] = ['p0' => $i, 'p1' => $j];
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // =============================================================================================================

        // EXPRESSION: $$ a_n = \frac{<par min="-2" max="2"/> n - <par min="-2" max="2"/>}{<par min="-2" max="2"/>} $$

        $values = [
            'type' => 3,
            'firstN' => 5,
            'firstValues' => [
                '1*p0 / p2 - p1 / p2',
                '2*p0 / p2 - p1 / p2',
                '3*p0 / p2 - p1 / p2'
            ],
            'subTheme' => 3,
            'studentVisible' => 1,
            'textBefore' => '',
            'body' => '$$ a_n = \frac{<par min="-2" max="2"/> n - <par min="-2" max="2"/>}{<par min="-2" max="2"/>} $$',
            'textAfter' => '',
            'difficulty' => 1,
            'condition_1' => 0,
            'variable' => 'x',
            'expression' => 'an = ((p0 n - p1)/(p2))',
            'standardized' => 'n p0 / p2 - p1 / p2',
        ];

        $parametersData = new ParametersData([
            'count' => 3,
            'complexity' => 125,
            'minMax' => [
                '0' => ['min' => -2, 'max' => 2],
                '1' => ['min' => -2, 'max' => 2],
                '2' => ['min' => -2, 'max' => 2],
            ]
        ]);

        $linearEquationTemplateNP = new ArithmeticSequenceTemplateNP($values);
        $linearEquationTemplateNP->setParametersData($parametersData);

        // Data for QUADRATIC_EQUATION condition matches find
        $data = [
            $this->constHelper::DIFFERENCE_VALIDATION => [
                $this->constHelper::DIFFERENCE_EXISTS => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 3; $i++) {
            for ($j = -2; $j < 3; $j++) {
                for ($k = -2; $k < 3; $k++) {
                    if ($k !== 0) {
                        $expectedRes[] = ['p0' => $i, 'p1' => $j, 'p2' => $k];
                    }
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);
    }

    public function testGeometricSequenceConditionMatches(): void
    {
        // EXPRESSION: $$ q_n = \big( \frac{<par min="-2" max="2"/>}{<par min="-2" max="2"/>} \big)^{n-1} $$

        $values = [
            'type' => 3,
            'firstN' => 5,
            'firstValues' => [
                'p0^(-1 + 1)*p1^(1 - 1)',
                'p0^(-1 + 2)*p1^(1 - 2)',
                'p0^(-1 + 3)*p1^(1 - 3)'
            ],
            'subTheme' => 3,
            'studentVisible' => 1,
            'textBefore' => '',
            'body' => '$$ q_n = \big( \frac{<par min="-2" max="2"/>}{<par min="-2" max="2"/>} \big)^{n-1} $$',
            'textAfter' => '',
            'difficulty' => 1,
            'condition_1' => 0,
            'variable' => 'x',
            'expression' => 'an = (p0/p1)^(n - 1)',
            'standardized' => 'p0^(-1 + n) p1^(1 - n)',
        ];

        $parametersData = new ParametersData([
            'count' => 2,
            'complexity' => 25,
            'minMax' => [
                '0' => ['min' => -2, 'max' => 2],
                '1' => ['min' => -2, 'max' => 2],
            ]
        ]);

        $linearEquationTemplateNP = new GeometricSequenceTemplateNP($values);
        $linearEquationTemplateNP->setParametersData($parametersData);

        // Data for QUADRATIC_EQUATION condition matches find
        $data = [
            $this->constHelper::QUOTIENT_VALIDATION => [
                $this->constHelper::QUOTIENT_EXISTS => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 3; $i++) {
            for ($j = -2; $j < 3; $j++) {
                if ($i !== 0 && $j !== 0) {
                    $expectedRes[] = ['p0' => $i, 'p1' => $j];
                }
            }
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);

        // =============================================================================================================

        // EXPRESSION: $$ q_n = <par min="-5" max="5"/> * 3^{1-n} $$

        $values = [
            'type' => 3,
            'firstN' => 5,
            'firstValues' => [
                '3^(1 - 1)*p0',
                '3^(1 - 1)*p0',
                '3^(1 - 1)*p0'
            ],
            'subTheme' => 3,
            'studentVisible' => 1,
            'textBefore' => '',
            'body' => '$$ q_n = <par min="-2" max="2"/> * 3^{1-n} $$',
            'textAfter' => '',
            'difficulty' => 1,
            'condition_1' => 0,
            'variable' => 'x',
            'expression' => 'qn = 3^(1 - n) p0',
            'standardized' => '3^(1 - n) p0',
        ];

        $parametersData = new ParametersData([
            'count' => 1,
            'complexity' => 5,
            'minMax' => [
                '0' => ['min' => -2, 'max' => 2],
            ]
        ]);

        $entityNP = new GeometricSequenceTemplateNP($values);
        $entityNP->setParametersData($parametersData);

        // Data for GEOMETRIC_SEQUENCE condition matches find
        $data = [
            $this->constHelper::QUOTIENT_VALIDATION => [
                $this->constHelper::QUOTIENT_EXISTS => ['data' => $entityNP]
            ]
        ];

        // Expected matches res
        $expectedRes = [];
        for ($i = -2; $i < 3; $i++) {
            $expectedRes[] = ['p0' => $i];
        }

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);
    }
}