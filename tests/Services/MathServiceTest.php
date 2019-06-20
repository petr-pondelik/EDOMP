<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 13.6.19
 * Time: 15:51
 */

namespace Tests\Model\Services;


use App\Helpers\ConstHelper;
use App\Helpers\LatexHelper;
use App\Helpers\StringsHelper;
use App\Model\Entity\ProblemFinal;
use App\Model\Repository\ProblemFinalRepository;
use App\Services\MathService;
use App\Services\NewtonApiClient;
use Nette\Utils\ArrayHash;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class MathServiceTest
 * @package App\AppTests\Services
 */
class MathServiceTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $newtonApiClientMock;

    /**
     * @var MockObject
     */
    protected $problemFinalRepositoryMock;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var LatexHelper
     */
    protected $latexHelper;

    /**
     * @var MathService
     */
    protected $mathService;

    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        parent::setUp();

        // Mock the NewtonApiClient
        $this->newtonApiClientMock = $this->getMockBuilder(NewtonApiClient::class)
            ->setMethods(['simplify'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for NewtonApiClient's simplify method
        $this->newtonApiClientMock->expects($this->any())
            ->method('simplify')
            ->willReturnCallback(static function ($arg) {
                switch ($arg){
                    case '15x + 10x':
                        return '25 x';
                    case '10':
                        return '10';
                    case '25 x - (10)':
                        return '25 x - 10';
                    case '5x + (15x - 4x) - 8':
                        return '16 x - 8';
                    case '2 + 5':
                        return '7';
                    case '16 x - 8 - (7)':
                        return '16 x - 15';
                    case 'x + 5 - (5 x + (15x - 4x)/(6))':
                        return '-35/6 x + 5';
                    case '- 5 + (2 - 8x + x)':
                        return '-7 x - 3';
                    case '-35/6 x + 5 - (-7 x - 3)':
                        return '7/6 x + 8';
                    case 'x^2 + 4x + x + 2':
                        return 'x^2 + 5 x + 2';
                    case '-4':
                        return '-4';
                    case 'x^2 + 5 x + 2 - (-4)':
                        return 'x^2 + 5 x + 6';
                    case '5x^2 + ( -x + 12x + 4 - 5 ) + 8 + 3x':
                        return '5 x^2 + 14 x + 7';
                    case '15 + 4':
                        return '19';
                    case '5 x^2 + 14 x + 7 - (19)':
                        return '5 x^2 + 14 x - 12';
                    case '5x^2 + ( -x + 12x + 4 - 5 ) + (8 + 3x)/(3)':
                        return '5 x^2 + 12 x + 5/3';
                    case '15 + ( 4 + 2 + 5x - x )':
                        return '4 x + 21';
                    case '5 x^2 + 12 x + 5/3 - (4 x + 21)':
                        return '5 x^2 + 8 x - 58/3';
                    case ' + 6':
                        return '6';
                    case ' - 12':
                        return '-12';
                    case ' - 58/3':
                        return '-58/3';
                    case '4x^2 + x':
                        return '4 x^2 + x';
                    case '0':
                        return '0';
                    case '4 x^2 + x - (0)':
                        return '4 x^2 + x';
                    case ' + x':
                        return 'x';
                    case '4x^2 + 5 x':
                        return '4 x^2 + 5 x';
                    case '4 x^2 + 5 x - (0)':
                        return '4 x^2 + 5 x';
                    case ' + 5 x':
                        return '5 x';
                    case '4x^2 - x':
                        return '4 x^2 - x';
                    case '4 x^2 - x - (0)':
                        return '4 x^2 - x';
                    case ' - x':
                        return '-x';
                    case ' + 14 x':
                        return '14 x';
                    case ' + 8 x':
                        return '8 x';
                    case '4x^2 - 5 x':
                        return '4 x^2 - 5 x';
                    case '4 x^2 - 5 x - (0)':
                        return '4 x^2 - 5 x';
                    case ' - 5 x':
                        return '-5 x';
                    case '3x^2 + x - 2':
                        return '3 x^2 + x - 2';
                    case '3 x^2 + x - 2 - (0)':
                        return '3 x^2 + x - 2';
                    case ' - 2':
                        return '-2';
                    case '4x^2 - 4x + 1':
                        return '4 x^2 - 4 x + 1';
                    case '4 x^2 - 4 x + 1 - (0)':
                        return '4 x^2 - 4 x + 1';
                    case ' + 1':
                        return '1';
                    case ' - 4 x':
                        return '-4 x';
                    case 'x^2 + (4 + 1) x + 2':
                        return 'x^2 + 5 x + 2';
                    case 'x^2 + 5 x + 2 - (0)':
                        return 'x^2 + 5 x + 2';
                    case ' + 2':
                        return '2';
                    case 'x^2 + x (4 + 1) + 4 + 2':
                        return 'x^2 + 5 x + 6';
                    case 'x^2 + 5 x + 6 - (0)':
                        return 'x^2 + 5 x + 6';
                    case 'x^2 -2 x + x - 2':
                        return 'x^2 - x - 2';
                    case 'x^2 - x - 2 - (0)':
                        return 'x^2 - x - 2';
                    case '(1)/(2) ( 2 x - 1 )^2 - ( (1)/(2) ( x + 1 ) )^2':
                        return '7/4 x^2 - 5/2 x + 1/4';
                    case '3 ( ( (1)/(2) x )^2 - ( (1)/(2) )^2 )':
                        return '3/4 x^2 - 3/4';
                    case '7/4 x^2 - 5/2 x + 1/4 - (3/4 x^2 - 3/4)':
                        return 'x^2 - 5/2 x + 1';
                    case ' - 5/2 x':
                        return '-5/2 x';
                    case '5 ( 5 ( 5 ( 5 x - 4 ) - 4 ) - 4 )':
                        return '625 x - 620';
                    case '5':
                        return '5';
                    case '625 x - 620 - (5)':
                        return '625 x - 625';
                    case '3 ( 2 ( 3 x - 6 ) - 2 ( 4 x - 5 ) + 1 ) - 3':
                        return '-6 x - 6';
                    case '6 ( 3 - 8 ( x - 3 ) )':
                        return '-48 x + 162';
                    case '-6 x - 6 - (-48 x + 162)':
                        return '42 x - 168';
                    default:
                        return null;
                }
            });

        // Mock the ProblemFinalRepository
        $this->problemFinalRepositoryMock = $this->getMockBuilder(ProblemFinalRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Instantiate ConstHelper
        $this->constHelper = new ConstHelper();

        // Instantiate StringsHelper
        $this->stringsHelper = new StringsHelper();

        // Instantiate LatexHelper
        $this->latexHelper = new LatexHelper();

        // Instantiate tested class
        $this->mathService = new MathService(
            $this->newtonApiClientMock, $this->problemFinalRepositoryMock, $this->constHelper, $this->stringsHelper, $this->latexHelper
        );
    }

    /**
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testStandardizeEquation(): void
    {
        // Test simple linear equation
        $equation = '$$ 15x + 10x = 10 $$';
        $this->assertEquals('25 x - 10',$this->mathService->standardizeEquation($equation));

        // Test medium linear equation
        $equation = '$$ 5x + \big(15x - 4x\big) - 8 = 2 + 5 $$';
        $this->assertEquals('16 x - 15', $this->mathService->standardizeEquation($equation));

        // Test hard linear equation
        $equation = '$$ x + 5 - \big(5 x + \frac{15x - 4x}{6}\big) = - 5 + \big(2 - 8x + x\big) $$';
        $this->assertEquals('7/6 x + 8', $this->mathService->standardizeEquation($equation));

        // Test simple quadratic equation
        $equation = '$$ x^2 + 4x + x + 2 = -4 $$';
        $this->assertEquals('x^2 + 5 x + 6', $this->mathService->standardizeEquation($equation));

        // Test medium quadratic equation
        $equation = '$$ 5x^2 + \big( -x + 12x + 4 - 5 \big) + 8 + 3x = 15 + 4 $$';
        $this->assertEquals('5 x^2 + 14 x - 12', $this->mathService->standardizeEquation($equation));

        // Test hard quadratic equation
        $equation = '$$ 5x^2 + \big( -x + 12x + 4 - 5 \big) + \frac{8 + 3x}{3} = 15 + \big( 4 + 2 + 5x - x \big) $$';
        $this->assertEquals('5 x^2 + 8 x - 58/3', $this->mathService->standardizeEquation($equation));

        // Test quadratic equation
        $equation = '$$ 4x^2 + x = 0 $$';
        $this->assertEquals('4 x^2 + x', $this->mathService->standardizeEquation($equation));
    }

    /**
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetDiscriminantExpression(): void
    {
        // Test simple quadratic equation
        $equation = '$$ x^2 + 4x + x + 2 = -4 $$';
        $this->assertEquals('(5)^2 - 4 * 1 * (6)', $this->mathService->getDiscriminantExpression($equation, 'x'));

        // Test medium quadratic equation
        $equation = '$$ 5x^2 + \big( -x + 12x + 4 - 5 \big) + 8 + 3x = 15 + 4 $$';
        $this->assertEquals('(14)^2 - 4 * 5 * (-12)', $this->mathService->getDiscriminantExpression($equation, 'x'));

        // Test hard quadratic equation
        $equation = '$$ 5x^2 + \big( -x + 12x + 4 - 5 \big) + \frac{8 + 3x}{3} = 15 + \big( 4 + 2 + 5x - x \big) $$';
        $this->assertEquals('(8)^2 - 4 * 5 * (-58/3)', $this->mathService->getDiscriminantExpression($equation, 'x'));

        // Test quadratic equation
        $equation = '$$ 4x^2 + x = 0 $$';
        $this->assertEquals('1^2 - 4 * 4 * 0', $this->mathService->getDiscriminantExpression($equation, 'x'));

        // Test quadratic equation
        $equation = '$$ 4x^2 - x = 0 $$';
        $this->assertEquals('(-1)^2 - 4 * 4 * 0', $this->mathService->getDiscriminantExpression($equation, 'x'));

        // Test quadratic equation
        $equation = '$$ 4x^2 + 5 x = 0 $$';
        $this->assertEquals('(5)^2 - 4 * 4 * 0', $this->mathService->getDiscriminantExpression($equation, 'x'));

        // Test quadratic equation
        $equation = '$$ 4x^2 - 5 x = 0 $$';
        $this->assertEquals('(-5)^2 - 4 * 4 * 0', $this->mathService->getDiscriminantExpression($equation, 'x'));

        // Test quadratic equation
        $equation = '$$ 3x^2 + x - 2 = 0 $$';
        $this->assertEquals('1^2 - 4 * 3 * (-2)', $this->mathService->getDiscriminantExpression($equation, 'x'));

        // Test quadratic equation
        $equation = '$$ 4x^2 - 4x + 1 = 0 $$';
        $this->assertEquals('(-4)^2 - 4 * 4 * (1)', $this->mathService->getDiscriminantExpression($equation, 'x'));

        // Test quadratic equation
        $equation = '$$ x^2 + (4 + 1) x + 2 = 0 $$';
        $this->assertEquals('(5)^2 - 4 * 1 * (2)', $this->mathService->getDiscriminantExpression($equation, 'x'));

        // Test quadratic equation
        $equation = '$$ x^2 + x (4 + 1) + 4 + 2 = 0 $$';
        $this->assertEquals('(5)^2 - 4 * 1 * (6)', $this->mathService->getDiscriminantExpression($equation, 'x'));

        // Test quadratic equation
        $equation = '$$ x^2 -2 x + x - 2 = 0 $$';
        $this->assertEquals('(-1)^2 - 4 * 1 * (-2)', $this->mathService->getDiscriminantExpression($equation, 'x'));

        // Test quadratic equation
        $equation = '$$ \frac{1}{2} \big( 2 x - 1 \big)^2 - \big( \frac{1}{2} \big( x + 1 \big) \big)^2 = 3 \big( \big( \frac{1}{2} x \big)^2 - \big( \frac{1}{2} \big)^2 \big) $$';
        $this->assertEquals('(-5/2)^2 - 4 * 1 * (1)', $this->mathService->getDiscriminantExpression($equation, 'x'));
    }

    /**
     * @throws \Exception
     */
    public function testEvaluate(): void
    {
        // Prepare linear equation
        $linearEquation = new ProblemFinal();
        $linearEquation->setBody('$$ 15x + 10x = 10 $$');
        $linearEquation->setVariable('x');
        $linearEquation->setId(1);

        // Evaluate linear equation and test it's result
        $res = $this->mathService->evaluate[$this->constHelper::LINEAR_EQ]($linearEquation);
        $this->assertEquals(ArrayHash::from(
            [
                'x' => 0.4
            ]
        ), $res);

        // Prepare linear equation
        $linearEquation->setBody('$$ 5 \big( 5 \big( 5 \big( 5 x - 4 \big) - 4 \big) - 4 \big) = 5 $$');

        // Evaluate linear equation and test it's result
        $res = $this->mathService->evaluate[$this->constHelper::LINEAR_EQ]($linearEquation);
        $this->assertEquals(ArrayHash::from(
            [
                'x' => 1
            ]
        ), $res);

        // Prepare linear equation
        $linearEquation->setBody('$$ 3 \big[ 2 \big( 3 x - 6 \big) - 2 \big( 4 x - 5 \big) + 1 \big] - 3 = 6 \big[ 3 - 8 \big( x - 3 \big) \big] $$');

        // Evaluate linear equation and test it's result
        $res = $this->mathService->evaluate[$this->constHelper::LINEAR_EQ]($linearEquation);
        $this->assertEquals(ArrayHash::from(
            [
                'x' => 4
            ]
        ), $res);

        // Prepare quadratic equation
        $quadraticEquation = new ProblemFinal();
        $quadraticEquation->setBody('$$ x^2 + 4x + x + 2 = -4 $$');
        $quadraticEquation->setVariable('x');
        $quadraticEquation->setId(2);

        // Evaluate quadratic equation and test it's result
        $res = $this->mathService->evaluate[$this->constHelper::QUADRATIC_EQ]($quadraticEquation);
        $this->assertEquals(ArrayHash::from(
            [
                'x_1' => -2.0,
                'x_2' => -3.0,
                'type' => 'double'
            ]
        ), $res);

        // Prepare second quadratic equation
        $quadraticEquation = new ProblemFinal();
        $quadraticEquation->setBody('$$ 4x^2 + x = 0 $$');
        $quadraticEquation->setVariable('x');
        $quadraticEquation->setId(3);

        // Prepare arithmetic sequence
        $arithmeticSequence = new ProblemFinal();
        $arithmeticSequence->setBody('$$ a_n = 2n + 7 $$');
        $arithmeticSequence->setVariable('n');
        $arithmeticSequence->setFirstN(5);
        $arithmeticSequence->setId(3);

        // Evaluate arithmetic sequence and test it's result
        $res = $this->mathService->evaluate[$this->constHelper::ARITHMETIC_SEQ]($arithmeticSequence);
        $this->assertEquals(ArrayHash::from(
            [
                'a_{1}' => 9,
                'a_{2}' => 11,
                'a_{3}' => 13,
                'a_{4}' => 15,
                'a_{5}' => 17,
                'Diference' => '2'
            ]
        ), $res);

        // Prepare geometric sequence
        $geometricSequence = new ProblemFinal();
        $geometricSequence->setBody('$$ q_n = \frac{\big( n + 1 \big)^{2}}{2} $$');
        $geometricSequence->setVariable('n');
        $geometricSequence->setFirstN(5);
        $geometricSequence->setId(4);

        // Evaluate geometric sequence and test it's result
        $res = $this->mathService->evaluate[$this->constHelper::GEOMETRIC_SEQ]($geometricSequence);
        $this->assertEquals(ArrayHash::from(
            [
                'q_{1}' => 2,
                'q_{2}' => 4.5,
                'q_{3}' => 8,
                'q_{4}' => 12.5,
                'q_{5}' => 18,
                'Kvocient' => '2.3'
            ]
        ), $res);
    }
}