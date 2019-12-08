<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.12.19
 * Time: 16:25
 */

namespace App\Tests\CoreModule\Helpers;

use App\CoreModule\Helpers\RegularExpressions;
use App\Tests\EDOMPUnitTestCase;

/**
 * Class RegularExpressionsHelperUnitTest
 * @package App\Tests\CoreModule\Helpers
 */
final class RegularExpressionsUnitTest extends EDOMPUnitTestCase
{
    /**
     * @var RegularExpressions
     */
    private $regularExpressions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->regularExpressions = $this->container->getByType(RegularExpressions::class);
    }

    public function testGetSequenceRE(): void
    {
        $variable = 'x';
        $this->assertEquals('^\s*\w' . $variable . '\s*=', $this->regularExpressions::getSequenceRE($variable));
    }

    public function testGetLinearEquationRE(): void
    {
        $variable = 'x';
        $expected =  '('
            . $this->regularExpressions::RE_EQUATION_SYMBOLS
            . '|'
            . $this->regularExpressions::RE_LOGARITHM
            . ')*'
            . $variable
            . '('
            . $this->regularExpressions::RE_EQUATION_SYMBOLS
            . '|'
            . $this->regularExpressions::RE_LOGARITHM
            . ')*';
        $this->assertEquals($expected, $this->regularExpressions::getLinearEquationRE($variable));
    }

    public function testGetQuadraticEquationRE(): void
    {
        $variable = 'y';
        $expected = '('
        . '('
        . $this->regularExpressions::RE_EQUATION_SYMBOLS
        . '|'
        . $this->regularExpressions::RE_LOGARITHM
        . ')*'
        . $variable . '\^\d'
        . ')*'
        . '('
        . $this->regularExpressions::RE_EQUATION_SYMBOLS
        . '|'
        . $this->regularExpressions::RE_LOGARITHM
        . ')*'
        . $variable . '\^2'
        . '('
        . '([\dp' . $variable . '\+\-\*\(\)\/\^])'
        . '|'
        . $this->regularExpressions::RE_LOGARITHM
        . ')*';
        $this->assertEquals($expected, $this->regularExpressions::getQuadraticEquationRE($variable));
    }
}