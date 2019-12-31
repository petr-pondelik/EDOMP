<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.12.19
 * Time: 21:53
 */

namespace App\Tests\TeacherModule\Services;


use App\TeacherModule\Services\MathService;
use App\Tests\EDOMPIntegrationTestCase;

/**
 * Class MathServiceTest
 * @package App\Tests\TeacherModule\Services
 */
final class MathServiceTest extends EDOMPIntegrationTestCase
{
    /**
     * @var MathService
     */
    protected $mathService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mathService = $this->container->getByType(MathService::class);
    }

//    public function testNormalizeCoefficient(): void
//    {
//
//    }
//
//    public function testFirstOperator(): void
//    {
//
//    }
//
//    public function testNegateOperators(): void
//    {
//
//    }
//
//    public function testIsEquation(): void
//    {
//
//    }
//
//    public function testGetEquationSides(): void
//    {
//
//    }
//
//    public function testContainsVariable(): void
//    {
//
//    }
//
//    public function testMergeEqSides(): void
//    {
//
//    }
//
//    public function testEvaluateExpression(): void
//    {
//
//    }
//
//    public function testProcessVariableFractions(): void
//    {
//
//    }
//
//    public function testExtractVariableCoefficients(): void
//    {
//
//    }
}