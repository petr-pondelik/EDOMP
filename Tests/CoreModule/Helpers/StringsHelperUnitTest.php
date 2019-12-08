<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.12.19
 * Time: 15:23
 */

namespace App\Tests\CoreModule\Helpers;


use App\CoreModule\Helpers\StringsHelper;
use App\Tests\EDOMPUnitTestCase;

/**
 * Class StringsHelperUnitTest
 * @package App\Tests\CoreModule\Helpers
 */
final class StringsHelperUnitTest extends EDOMPUnitTestCase
{
    /**
     * @var StringsHelper
     */
    private $stringsHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stringsHelper = $this->container->getByType(StringsHelper::class);
    }

    public function testRemoveWhiteSpaces(): void
    {
        $str = '(1 + p0) x^2 + (-1 - p0 + p1) x + 1';
        $str = $this->stringsHelper::removeWhiteSpaces($str);

        $expected = '(1+p0)x^2+(-1-p0+p1)x+1';
        $this->assertEquals($expected, $str);
    }

    public function testDeduplicateWhiteSpaces(): void
    {
        $str = '  (1  +  p0  )   x^2  + (  -1 - p0 + p1) x +   1  ';
        $str = $this->stringsHelper::deduplicateWhiteSpaces($str);

        $expected = ' (1 + p0 ) x^2 + ( -1 - p0 + p1) x + 1 ';
        $this->assertEquals($expected, $str);
    }

    public function testTrimOperators(): void
    {
        $str = '+ - 5 x - 4 +';
        $trimmed = $this->stringsHelper::trimOperators($str);

        $expected = '5 x - 4';
        $this->assertEquals($expected, $trimmed);

        $str = '+ - 5 x - 4 +';
        $expected = '- 5 x - 4';
        $trimmed = $this->stringsHelper::trimOperators($str, true);
        $this->assertEquals($expected, $trimmed);
    }

    public function testNormalizeOperators(): void
    {
        $str = '+ - 5 x - - 4 + + 8 x - + 1 + 6';
        $expected = '- 5 x + 4 + 8 x - 1 + 6';

        $str = $this->stringsHelper::normalizeOperators($str);
        $this->assertEquals($expected, $str);

        $str = '4 + (+ 5 - 2) + (5x + 6)';
        $expected = '4 + ( 5 - 2) + (5x + 6)';

        $str = $this->stringsHelper::normalizeOperators($str);
        $this->assertEquals($expected, $str);
    }

    public function testTrim(): void
    {
        $str = '   x^2 + (-1 - p0 + p1) x + 1  ';
        $expected = 'x^2 + (-1 - p0 + p1) x + 1';

        $trimmed = $this->stringsHelper::trim($str);
        $this->assertEquals($expected, $trimmed);

        $str = '   (x^2 + (-1 - p0 + p1) x + 1)  ';
        $expected = 'x^2 + (-1 - p0 + p1) x + 1';

        $trimmed = $this->stringsHelper::trim($str);
        $this->assertEquals($expected, $trimmed);

        $str = '   \( x^2 + (-1 - p0 + p1) x + 1 \)  ';
        $expected = 'x^2 + (-1 - p0 + p1) x + 1';

        $trimmed = $this->stringsHelper::trim($str, $this->stringsHelper::LATEX_INLINE);
        $this->assertEquals($expected, $trimmed);

        $str = '   + x^2 + (-1 - p0 + p1) x + 1 + ';
        $expected = 'x^2 + (-1 - p0 + p1) x + 1';

        $trimmed = $this->stringsHelper::trim($str, $this->stringsHelper::ADDITION);
        $this->assertEquals($expected, $trimmed);

        $str = '  - x^2 + (-1 - p0 + p1) x + 1 - ';
        $expected = 'x^2 + (-1 - p0 + p1) x + 1';

        $trimmed = $this->stringsHelper::trim($str, $this->stringsHelper::SUBTRACTION);
        $this->assertEquals($expected, $trimmed);
    }

    public function testWrap(): void
    {
        $str = 'x^2 + (-1 - p0 + p1) x + 1';
        $expected = '(x^2 + (-1 - p0 + p1) x + 1)';

        $str = $this->stringsHelper::wrap($str);
        $this->assertEquals($expected, $str);
    }

    public function testFillMultipliers(): void
    {
        // Test with 2 multipliers

        $str = 'x^2 + (-1 - 5 4) + x + 1';
        $expected = 'x^2 + (-1 - 5*4) + x + 1';

        $str = $this->stringsHelper::fillMultipliers($str);
        $this->assertEquals($expected, $str);

        $str = 'x^2 + (-1 - p0 p1) + x + 1';
        $expected = 'x^2 + (-1 - p0*p1) + x + 1';

        $str = $this->stringsHelper::fillMultipliers($str);
        $this->assertEquals($expected, $str);

        $str = 'x^2 + (-1 - -5 -4) + x + 1';
        $expected = 'x^2 + (-1 - -5*-4) + x + 1';

        $str = $this->stringsHelper::fillMultipliers($str);
        $this->assertEquals($expected, $str);

        $str = 'x^2 + (-1 - p0 -p1) + x + 1';
        $expected = 'x^2 + (-1 - p0*-p1) + x + 1';

        $str = $this->stringsHelper::fillMultipliers($str);
        $this->assertEquals($expected, $str);

        $str = 'x^2 + (-1 - 4 -p1) + x + 1';
        $expected = 'x^2 + (-1 - 4*-p1) + x + 1';

        $str = $this->stringsHelper::fillMultipliers($str);
        $this->assertEquals($expected, $str);

        $str = 'x^2 + (-1 + -p0 4) + x + 1';
        $expected = 'x^2 + (-1 + -p0*4) + x + 1';

        $str = $this->stringsHelper::fillMultipliers($str);
        $this->assertEquals($expected, $str);

        // =========================================================
        // Test with 3 multipliers

        $str = 'x^2 + (-1 - 5 4 2) + x + 1';
        $expected = 'x^2 + (-1 - 5*4*2) + x + 1';

        $str = $this->stringsHelper::fillMultipliers($str);
        $this->assertEquals($expected, $str);

        $str = 'x^2 + (-1 - p0 p1 p2) + x + 1';
        $expected = 'x^2 + (-1 - p0*p1*p2) + x + 1';

        $str = $this->stringsHelper::fillMultipliers($str);
        $this->assertEquals($expected, $str);

        $str = 'x^2 + (-1 - -5 -4 -2) + x + 1';
        $expected = 'x^2 + (-1 - -5*-4*-2) + x + 1';

        $str = $this->stringsHelper::fillMultipliers($str);
        $this->assertEquals($expected, $str);

        $str = 'x^2 + (-1 - p0 -p1 -p2) + x + 1';
        $expected = 'x^2 + (-1 - p0*-p1*-p2) + x + 1';

        $str = $this->stringsHelper::fillMultipliers($str);
        $this->assertEquals($expected, $str);

        $str = 'x^2 + (-1 - 4 -p1 -1) + x + 1';
        $expected = 'x^2 + (-1 - 4*-p1*-1) + x + 1';

        $str = $this->stringsHelper::fillMultipliers($str);
        $this->assertEquals($expected, $str);

        $str = 'x^2 + (-1 + -p0 4 2) + x + 1';
        $expected = 'x^2 + (-1 + -p0*4*2) + x + 1';

        $str = $this->stringsHelper::fillMultipliers($str);
        $this->assertEquals($expected, $str);

        // =========================================================
        // Test with variable

        $str = 'x^2 + (-1 - 5 x) + x + 1';
        $expected = 'x^2 + (-1 - 5*x) + x + 1';

        $str = $this->stringsHelper::fillMultipliers($str, 'x');
        $this->assertEquals($expected, $str);

        $str = 'x^2 + (-1 - 5)x + 1';
        $expected = 'x^2 + (-1 - 5)*x + 1';

        $str = $this->stringsHelper::fillMultipliers($str, 'x');
        $this->assertEquals($expected, $str);
    }

    public function testRemoveSubstring(): void
    {
        $str = 'x^2 + (-1 - 5 x)/(5 + 3) +  x + 1';
        $expected = 'x^2 + + x + 1';

        $str = $this->stringsHelper::removeSubstring($str, '(-1 - 5 x)/(5 + 3)');
        $this->assertEquals($expected, $str);
    }
}