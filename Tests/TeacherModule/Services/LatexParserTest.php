<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.12.19
 * Time: 17:20
 */

namespace App\Tests\TeacherModule\Services;


use App\TeacherModule\Services\LatexParser;
use App\Tests\EDOMPIntegrationTestCase;

/**
 * Class LatexParserTest
 * @package App\Tests\TeacherModule\Services
 */
final class LatexParserTest extends EDOMPIntegrationTestCase
{
    /**
     * @var LatexParser
     */
    protected $latexParser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->latexParser = $this->container->getByType(LatexParser::class);
    }

    public function testTrim(): void
    {
        $expected = '\frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0';

        $str = '$$ \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0 $$';
        $this->assertEquals($expected, $this->latexParser::trim($str));

        $str = '   $$ \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0 $$   ';
        $this->assertEquals($expected, $this->latexParser::trim($str));

        $str = '   $$   \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0    $$   ';
        $this->assertEquals($expected, $this->latexParser::trim($str));

        $str = '   \[   \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0    \]   ';
        $this->assertEquals($expected, $this->latexParser::trim($str));

        $str = '   \begin{displaymath}   \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0    \end{displaymath}   ';
        $this->assertEquals($expected, $this->latexParser::trim($str));

        $str = '   \begin{equation}   \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0    \end{equation}   ';
        $this->assertEquals($expected, $this->latexParser::trim($str));

        $str = '   $   \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0    $   ';
        $this->assertEquals($expected, $this->latexParser::trim($str));

        $str = '   \(   \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0    \)   ';
        $this->assertEquals($expected, $this->latexParser::trim($str));

        $str = '   \begin{math}   \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0    \end{math}   ';
        $this->assertEquals($expected, $this->latexParser::trim($str));
    }

    public function testParseParentheses(): void
    {
        $str = '$$ <par min="-2" max="2"/> \big( 4x - <par min="-3" max="3"/> \big) \big( x + 1 \big) = \big( <par min="-4" max="4"/> + 1 \big) \big( x - 1 \big) - 7 $$';
        $expected = '$$ <par min="-2" max="2"/> ( 4x - <par min="-3" max="3"/> ) ( x + 1 ) = ( <par min="-4" max="4"/> + 1 ) ( x - 1 ) - 7 $$';
        $this->assertEquals($expected, $this->latexParser::parseParentheses($str));

        $str = '$$ <par min="-2" max="2"/> \Big( 4x - <par min="-3" max="3"/> \Big) \Big( x + 1 \Big) = \Big( <par min="-4" max="4"/> + 1 \Big) \Big( x - 1 \Big) - 7 $$';
        $expected = '$$ <par min="-2" max="2"/> ( 4x - <par min="-3" max="3"/> ) ( x + 1 ) = ( <par min="-4" max="4"/> + 1 ) ( x - 1 ) - 7 $$';
        $this->assertEquals($expected, $this->latexParser::parseParentheses($str));

        $str = '$$ <par min="-2" max="2"/> \bigg( 4x - <par min="-3" max="3"/> \bigg) \bigg( x + 1 \bigg) = \bigg( <par min="-4" max="4"/> + 1 \bigg) \bigg( x - 1 \bigg) - 7 $$';
        $expected = '$$ <par min="-2" max="2"/> ( 4x - <par min="-3" max="3"/> ) ( x + 1 ) = ( <par min="-4" max="4"/> + 1 ) ( x - 1 ) - 7 $$';
        $this->assertEquals($expected, $this->latexParser::parseParentheses($str));

        $str = '$$ <par min="-2" max="2"/> \Bigg( 4x - <par min="-3" max="3"/> \Bigg) \Bigg( x + 1 \Bigg) = \Bigg( <par min="-4" max="4"/> + 1 \Bigg) \Bigg( x - 1 \Bigg) - 7 $$';
        $expected = '$$ <par min="-2" max="2"/> ( 4x - <par min="-3" max="3"/> ) ( x + 1 ) = ( <par min="-4" max="4"/> + 1 ) ( x - 1 ) - 7 $$';
        $this->assertEquals($expected, $this->latexParser::parseParentheses($str));

        $str = '$$ <par min="-2" max="2"/> \big[ 4x - <par min="-3" max="3"/> \big] \big[ x + 1 \big] = \big[ <par min="-4" max="4"/> + 1 \big] \big[ x - 1 \big] - 7 $$';
        $expected = '$$ <par min="-2" max="2"/> ( 4x - <par min="-3" max="3"/> ) ( x + 1 ) = ( <par min="-4" max="4"/> + 1 ) ( x - 1 ) - 7 $$';
        $this->assertEquals($expected, $this->latexParser::parseParentheses($str));

        $str = '$$ <par min="-2" max="2"/> \Big[ 4x - <par min="-3" max="3"/> \Big] \Big[ x + 1 \Big] = \Big[ <par min="-4" max="4"/> + 1 \Big] \Big[ x - 1 \Big] - 7 $$';
        $expected = '$$ <par min="-2" max="2"/> ( 4x - <par min="-3" max="3"/> ) ( x + 1 ) = ( <par min="-4" max="4"/> + 1 ) ( x - 1 ) - 7 $$';
        $this->assertEquals($expected, $this->latexParser::parseParentheses($str));

        $str = '$$ <par min="-2" max="2"/> \bigg[ 4x - <par min="-3" max="3"/> \bigg] \bigg[ x + 1 \bigg] = \bigg[ <par min="-4" max="4"/> + 1 \bigg] \bigg[ x - 1 \bigg] - 7 $$';
        $expected = '$$ <par min="-2" max="2"/> ( 4x - <par min="-3" max="3"/> ) ( x + 1 ) = ( <par min="-4" max="4"/> + 1 ) ( x - 1 ) - 7 $$';
        $this->assertEquals($expected, $this->latexParser::parseParentheses($str));

        $str = '$$ <par min="-2" max="2"/> \Bigg[ 4x - <par min="-3" max="3"/> \Bigg] \Bigg[ x + 1 \Bigg] = \Bigg[ <par min="-4" max="4"/> + 1 \Bigg] \Bigg[ x - 1 \Bigg] - 7 $$';
        $expected = '$$ <par min="-2" max="2"/> ( 4x - <par min="-3" max="3"/> ) ( x + 1 ) = ( <par min="-4" max="4"/> + 1 ) ( x - 1 ) - 7 $$';
        $this->assertEquals($expected, $this->latexParser::parseParentheses($str));

        $str = '$$ <par min="-2" max="2"/> \big\{ 4x - <par min="-3" max="3"/> \big\} \big\{ x + 1 \big\} = \big\{ <par min="-4" max="4"/> + 1 \big\} \big\{ x - 1 \big\} - 7 $$';
        $expected = '$$ <par min="-2" max="2"/> ( 4x - <par min="-3" max="3"/> ) ( x + 1 ) = ( <par min="-4" max="4"/> + 1 ) ( x - 1 ) - 7 $$';
        $this->assertEquals($expected, $this->latexParser::parseParentheses($str));

        $str = '$$ <par min="-2" max="2"/> \Big\{ 4x - <par min="-3" max="3"/> \Big\} \Big\{ x + 1 \Big\} = \Big\{ <par min="-4" max="4"/> + 1 \Big\} \Big\{ x - 1 \Big\} - 7 $$';
        $expected = '$$ <par min="-2" max="2"/> ( 4x - <par min="-3" max="3"/> ) ( x + 1 ) = ( <par min="-4" max="4"/> + 1 ) ( x - 1 ) - 7 $$';
        $this->assertEquals($expected, $this->latexParser::parseParentheses($str));

        $str = '$$ <par min="-2" max="2"/> \bigg\{ 4x - <par min="-3" max="3"/> \bigg\} \bigg\{ x + 1 \bigg\} = \bigg\{ <par min="-4" max="4"/> + 1 \bigg\} \bigg\{ x - 1 \bigg\} - 7 $$';
        $expected = '$$ <par min="-2" max="2"/> ( 4x - <par min="-3" max="3"/> ) ( x + 1 ) = ( <par min="-4" max="4"/> + 1 ) ( x - 1 ) - 7 $$';
        $this->assertEquals($expected, $this->latexParser::parseParentheses($str));

        $str = '$$ <par min="-2" max="2"/> \Bigg\{ 4x - <par min="-3" max="3"/> \Bigg\} \Bigg\{ x + 1 \Bigg\} = \Bigg\{ <par min="-4" max="4"/> + 1 \Bigg\} \Bigg\{ x - 1 \Bigg\} - 7 $$';
        $expected = '$$ <par min="-2" max="2"/> ( 4x - <par min="-3" max="3"/> ) ( x + 1 ) = ( <par min="-4" max="4"/> + 1 ) ( x - 1 ) - 7 $$';
        $this->assertEquals($expected, $this->latexParser::parseParentheses($str));

        $str = '$$ <par min="-2" max="2"/> \big \langle 4x - <par min="-3" max="3"/> \big \rangle \big \langle x + 1 \big \rangle = \big \langle <par min="-4" max="4"/> + 1 \big \rangle \big \langle x - 1 \big \rangle - 7 $$';
        $expected = '$$ <par min="-2" max="2"/> ( 4x - <par min="-3" max="3"/> ) ( x + 1 ) = ( <par min="-4" max="4"/> + 1 ) ( x - 1 ) - 7 $$';
        $this->assertEquals($expected, $this->latexParser::parseParentheses($str));

        $str = '$$ <par min="-2" max="2"/> \Big \langle 4x - <par min="-3" max="3"/> \Big \rangle \Big \langle x + 1 \Big \rangle = \Big \langle <par min="-4" max="4"/> + 1 \Big \rangle \Big \langle x - 1 \Big \rangle - 7 $$';
        $expected = '$$ <par min="-2" max="2"/> ( 4x - <par min="-3" max="3"/> ) ( x + 1 ) = ( <par min="-4" max="4"/> + 1 ) ( x - 1 ) - 7 $$';
        $this->assertEquals($expected, $this->latexParser::parseParentheses($str));

        $str = '$$ <par min="-2" max="2"/> \bigg \langle 4x - <par min="-3" max="3"/> \bigg \rangle \bigg \langle x + 1 \bigg \rangle = \bigg \langle <par min="-4" max="4"/> + 1 \bigg \rangle \bigg \langle x - 1 \bigg \rangle - 7 $$';
        $expected = '$$ <par min="-2" max="2"/> ( 4x - <par min="-3" max="3"/> ) ( x + 1 ) = ( <par min="-4" max="4"/> + 1 ) ( x - 1 ) - 7 $$';
        $this->assertEquals($expected, $this->latexParser::parseParentheses($str));

        $str = '$$ <par min="-2" max="2"/> \Bigg \langle 4x - <par min="-3" max="3"/> \Bigg \rangle \Bigg \langle x + 1 \Bigg \rangle = \Bigg \langle <par min="-4" max="4"/> + 1 \Bigg \rangle \Bigg \langle x - 1 \Bigg \rangle - 7 $$';
        $expected = '$$ <par min="-2" max="2"/> ( 4x - <par min="-3" max="3"/> ) ( x + 1 ) = ( <par min="-4" max="4"/> + 1 ) ( x - 1 ) - 7 $$';
        $this->assertEquals($expected, $this->latexParser::parseParentheses($str));
    }

    public function testParseFractions(): void
    {
        $str = '$$ \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0 $$';
        $expected = '$$ ((<par min="-3" max="3"/> + 5)/( <par min="-5" max="5"/> (x - 2))) - ((1)/(x + 2)) + ((x^2 - 8)/(x^2 - 4)) = 0 $$';
        $this->assertEquals($expected, $this->latexParser::parseFractions($str));
    }

    public function testParseSuperscripts(): void
    {
        $str = 'q_n = \big( \frac{<par min="-5" max="5"/>}{<par min="-5" max="5"/>} \big)^{n-1}';
        $expected = 'q_n = \big( \frac{<par min="-5" max="5"/>}{<par min="-5" max="5"/>} \big)^(n-1)';
        $this->assertEquals($expected, $this->latexParser::parseSuperscripts($str));
    }

    public function testParseLogarithm(): void
    {
        $str = 'q_n = \big( \frac{<par min="-5" max="5"/>}{<par min="-5" max="5"/>} \big)^{n-1} + \log100';
        $expected = 'q_n = \big( \frac{<par min="-5" max="5"/>}{<par min="-5" max="5"/>} \big)^{n-1} + log(100)';
        $this->assertEquals($expected, $this->latexParser::parseLogarithm($str));
    }

    public function testLatexWrapped(): void
    {
        $str = '$$ \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0 $$';
        $this->assertTrue($this->latexParser::latexWrapped($str));

        $str = '   $$ \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0 $$   ';
        $this->assertTrue($this->latexParser::latexWrapped($str));

        $str = '   $$   \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0    $$   ';
        $this->assertTrue($this->latexParser::latexWrapped($str));

        $str = '   \[   \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0    \]   ';
        $this->assertTrue($this->latexParser::latexWrapped($str));

        $str = '   \begin{displaymath}   \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0    \end{displaymath}   ';
        $this->assertTrue($this->latexParser::latexWrapped($str));

        $str = '   \begin{equation}   \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0    \end{equation}   ';
        $this->assertTrue($this->latexParser::latexWrapped($str));

        $str = '   $   \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0    $   ';
        $this->assertTrue($this->latexParser::latexWrapped($str));

        $str = '   \(   \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0    \)   ';
        $this->assertTrue($this->latexParser::latexWrapped($str));

        $str = '   \begin{math}   \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0    \end{math}   ';
        $this->assertTrue($this->latexParser::latexWrapped($str));

        $str = '\frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0';
        $this->assertFalse($this->latexParser::latexWrapped($str));
    }

    public function testRemoveZeroMultipliedBrackets(): void
    {
        $str = '$$ 0 \big( 4x - 2 \big) \big( x + 1 \big) = \big( 1 + 1 \big) \big( x - 1 \big) - 7 $$';
        $expected = '$$  0  = \big( 1 + 1 \big) \big( x - 1 \big) - 7 $$';
        $this->assertEquals($expected, $this->latexParser->removeZeroMultipliedBrackets($str));

        $str = '$$ \big( 4x - 2 \big) \big( x + 1 \big) = 0 \big( 1 + 1 \big) \big( x - 1 \big) - 7 $$';
        $expected = '$$ \big( 4x - 2 \big) \big( x + 1 \big) =  0  - 7 $$';
        $this->assertEquals($expected, $this->latexParser->removeZeroMultipliedBrackets($str));

        $str = '$$ 0 \big( 4x - 2 \big) + 4 = 0 \big( 1 + 1 \big) \big( x - 1 \big) - 7 $$';
        $expected = '$$  0  + 4 =  0  - 7 $$';
        $this->assertEquals($expected, $this->latexParser->removeZeroMultipliedBrackets($str));

        $str = '$$ \big( 4x - 2 \big) + 4 = \big( 1 + 1 \big) + 0 \big( x - 1 \big) - 7 $$';
        $expected = '$$ \big( 4x - 2 \big) + 4 = \big( 1 + 1 \big)  + 0  - 7 $$';
        $this->assertEquals($expected, $this->latexParser->removeZeroMultipliedBrackets($str));
    }

    public function testRemoveZeroMultipliedFractions(): void
    {
        $str = '0 \frac{4 + 5}{ 2 (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0';
        $expected = '  0  - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0';
        $this->assertEquals($expected, $this->latexParser->removeZeroMultipliedFractions($str));

        $str = '\frac{4 + 5}{ 2 (x - 2)} - 0 \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0';
        $expected = '\frac{4 + 5}{ 2 (x - 2)}  - 0  + \frac{x^2 - 8}{x^2 - 4} = 0';
        $this->assertEquals($expected, $this->latexParser->removeZeroMultipliedFractions($str));

        $str = '\frac{4 + 5}{ 2 (x - 2)} + 0 \frac{x^2 - 8}{x^2 - 4} = 0';
        $expected = '\frac{4 + 5}{ 2 (x - 2)}  + 0  = 0';
        $this->assertEquals($expected, $this->latexParser->removeZeroMultipliedFractions($str));
    }

    public function testRemoveZeroMultipliedValues(): void
    {
        $str = '0 5 - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0';
        $expected = '  0  - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0';
        $this->assertEquals($expected, $this->latexParser->removeZeroMultipliedValues($str));

        $str = '\frac{4 + 5}{ 2 (x - 2)} - 0*2 + \frac{x^2 - 8}{x^2 - 4} = 0';
        $expected = '\frac{4 + 5}{ 2 (x - 2)}  - 0  + \frac{x^2 - 8}{x^2 - 4} = 0';
        $this->assertEquals($expected, $this->latexParser->removeZeroMultipliedValues($str));
    }

    public function testRemoveZeroValues(): void
    {
        $str = '  0  - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0';
        $expected = ' - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0';
        $this->assertEquals($expected, $this->latexParser->removeZeroValues($str));

        $str = '\frac{4 + 5}{ 2 (x - 2)}  - 0  + \frac{x^2 - 8}{x^2 - 4} = 0';
        $expected = '\frac{4 + 5}{ 2 (x - 2)}  + \frac{x^2 - 8}{x^2 - 4} = 0';
        $this->assertEquals($expected, $this->latexParser->removeZeroValues($str));

        $str = '\frac{4 + 5}{ 2 (x - 2)}  + 0  = 0';
        $expected = '\frac{4 + 5}{ 2 (x - 2)}  = 0';
        $this->assertEquals($expected, $this->latexParser->removeZeroValues($str));
    }

    public function testParse(): void
    {
        $str = '$$ \frac{<par min="-2" max="2"/> + 5}{ \big( x + 2 \big) \big( x - <par min="-2" max="2"/> \big) } - \frac{1}{x + 2} + \frac{2}{4} = 0 $$';
        $expected = '((<par min="-2" max="2"/> + 5)/( ( x + 2 ) ( x - <par min="-2" max="2"/> ) )) - ((1)/(x + 2)) + ((2)/(4)) = 0';
        $this->assertEquals($expected, $this->latexParser::parse($str));
    }

    public function testPostprocessFinalBody(): void
    {
        $str = '$$ 0 \frac{4 + 5}{ 2 (x - 2)} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0 $$';
        $expected = '$$ - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0 $$';
        $this->assertEquals($expected, $this->latexParser->postprocessFinalBody($str));

        $str = '$$ 1 = \frac{x - 0 + -4}{ 4 - -3 \big( 3 x + 3 \big) } + \frac{-5}{x + 4} $$';
        $expected = '$$ 1 = \frac{x - 4}{ 4 + 3 \big( 3 x + 3 \big) } + \frac{-5}{x + 4} $$';
        $this->assertEquals($expected, $this->latexParser->postprocessFinalBody($str));
    }
}