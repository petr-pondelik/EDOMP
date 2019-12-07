<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.12.19
 * Time: 17:15
 */

namespace App\Tests\TeacherModule\Services;

use App\CoreModule\Helpers\ConstHelper;
use App\TeacherModule\Model\NonPersistent\Entity\LinearEquationTemplateNP;
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


        $values = ArrayHash::from([
            'type' => 1,
            'subTheme' => 1,
            'studentVisible' => 1,
            'textBefore' => '',
            'body' => '$$ 15 x + 5 x + 5 = \frac{<par min="-2" max="4"/>}{<par min="-2" max="4"/>} $$',
            'textAfter' => '',
            'difficulty' => 1,
            'condition_1' => 1,
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


        $values = ArrayHash::from([
            'type' => 1,
            'subTheme' => 1,
            'studentVisible' => 1,
            'textBefore' => '',
            'body' => '$$ 15 x + 5 x + 5 = \frac{<par min="-2" max="4"/>}{<par min="-2" max="4"/>} $$',
            'textAfter' => '',
            'difficulty' => 1,
            'condition_1' => 2,
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
                $this->constHelper::RESULT_ZERO => ['data' => $linearEquationTemplateNP]
            ]
        ];

        // Expected matches res
        $expectedRes = null;

        // Get matches
        $res = $this->conditionService->findConditionsMatches($data);

        // Test real matches against the expected matches
        $this->assertEquals($expectedRes, $res);


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

    }

    public function testDiscriminantConditionMatches(): void
    {

    }

    public function testArithmeticSequenceConditionMatches(): void
    {

    }

    public function testGeometricSequenceConditionMatches(): void
    {

    }
}