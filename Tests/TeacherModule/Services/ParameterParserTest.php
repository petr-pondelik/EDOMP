<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.12.19
 * Time: 20:58
 */

namespace App\Tests\TeacherModule\Services;


use App\TeacherModule\Services\ParameterParser;
use App\Tests\EDOMPIntegrationTestCase;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class ParameterParserTest
 * @package App\Tests\TeacherModule\Services
 */
final class ParameterParserTest extends EDOMPIntegrationTestCase
{
    /**
     * @var ParameterParser
     */
    protected $parameterParser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parameterParser = $this->container->getByType(ParameterParser::class);
    }

    public function testSplitByParameters(): void
    {
        $str = '$$ 1 = \frac{x - <par min="-5" max="5"/> + 4}{ 4 <par min="-3" max="3"/> \big( 3 x + 3 \big) } + \frac{<par min="-4" max="4"/>}{x + <par min="-4" max="4"/>} $$';
        $expected = [
            '$$ 1 = \frac{x - ',
            '<par min="-5" max="5"/>',
            ' + 4}{ 4 ',
            '<par min="-3" max="3"/>',
            ' \big( 3 x + 3 \big) } + \frac{',
            '<par min="-4" max="4"/>',
            '}{x + ',
            '<par min="-4" max="4"/>',
            '} $$'
        ];
        $this->assertEquals($expected, $this->parameterParser::splitByParameters($str));

        $str = '$$ <par min="-5" max="5"/> x^2 + x + 5 = 4 $$';
        $expected = [
            '$$ ',
            '<par min="-5" max="5"/>',
            ' x^2 + x + 5 = 4 $$'
        ];
        $this->assertEquals($expected, $this->parameterParser::splitByParameters($str));
    }

    public function testExtractParAttr(): void
    {
        $par = '<par min="-5" max="5"/>';

        $expected = -5;
        $this->assertEquals($expected, $this->parameterParser::extractParAttr($par, 'min'));

        $expected = 5;
        $this->assertEquals($expected, $this->parameterParser::extractParAttr($par, 'max'));
    }

    public function testFindParametersAll(): void
    {
        $str = '$$ 1 = \frac{x - <par min="-5" max="5"/> + 4}{ 4 <par min="-3" max="3"/> \big( 3 x + 3 \big) } + \frac{<par min="-4" max="4"/>}{x + <par min="-4" max="4"/>} $$';
        $expected = [
            '<par min="-5" max="5"/>',
            '<par min="-3" max="3"/>',
            '<par min="-4" max="4"/>',
            '<par min="-4" max="4"/>'
        ];
        $found = $this->parameterParser::findParametersAll($str);
        foreach ($found as $key => $item) {
            $found[$key] = Strings::trim($item);
        }
        foreach ($expected as $item) {
            $this->assertContains($item, $found);
        }

        $str = '$$ 1 = \frac{x - <par min="" max="5"/> + 4}{ 4 <par min="-3" max=""/> \big( 3 x + 3 \big) } + \frac{<par min="-" max="+"/>}{x + <par min="" max=""/>} $$';
        $expected = [
            '<par min="" max="5"/>',
            '<par min="-3" max=""/>',
            '<par min="-" max="+"/>',
            '<par min="" max=""/>'
        ];
        $found = $this->parameterParser::findParametersAll($str);
        foreach ($found as $key => $item) {
            $found[$key] = Strings::trim($item);
        }
        foreach ($expected as $item) {
            $this->assertContains($item, $found);
        }

        $str = '$$ 1 = \frac{x - <par min= max=/> + 4}{ 4 <par min max/> \big( 3 x + 3 \big) } + \frac{<par max="+"/>}{x + <par min=""/>} $$';
        $expected = [
            '<par min= max=/>',
            '<par min max/>',
            '<par max="+"/>',
            '<par min=""/>'
        ];
        $found = $this->parameterParser::findParametersAll($str);
        foreach ($found as $key => $item) {
            $found[$key] = Strings::trim($item);
        }
        foreach ($expected as $item) {
            $this->assertContains($item, $found);
        }

        $str = '$$ 1 = \frac{x - <par min=/> + 4}{ 4 <par max=/> \big( 3 x + 3 \big) } + \frac{<par max/>}{x + <par min/>} $$';
        $expected = [
            '<par min=/>',
            '<par max=/>',
            '<par max/>',
            '<par min/>'
        ];
        $found = $this->parameterParser::findParametersAll($str);
        foreach ($found as $key => $item) {
            $found[$key] = Strings::trim($item);
        }
        foreach ($expected as $item) {
            $this->assertContains($item, $found);
        }

        $str = '$$ 1 = \frac{x - <par /> + 4}{ 4 <par/> \big( 3 x + 3 \big) } + \frac{<par >}{x + <par>} $$';
        $expected = [
            '<par />',
            '<par/>',
            '<par >',
            '<par>'
        ];
        $found = $this->parameterParser::findParametersAll($str);
        foreach ($found as $key => $item) {
            $found[$key] = Strings::trim($item);
        }
        foreach ($expected as $item) {
            $this->assertContains($item, $found);
        }

        $str = '$$ 1 = \frac{x - <par> + 4}{ 4 <par \big( 3 x + 3 \big) } $$';
        $expected = [
            '<par>',
            '<par',
        ];
        $found = $this->parameterParser::findParametersAll($str);
        foreach ($found as $key => $item) {
            $found[$key] = Strings::trim($item);
        }
        foreach ($expected as $item) {
            $this->assertContains($item, $found);
        }
    }

    public function testPassValues(): void
    {
        $str = '((x - p0)/( - p1 + 3 ( p2 x + 3 ) )) + ((-p3)/(x + 4)) + p4';
        $values = [
            'p0' => 4,
            'p1' => -2,
            'p2' => 5,
            'p3' => 2,
            'p4' => -1
        ];
        $expected = '((x - 4)/( 2 + 3 ( 5 x + 3 ) )) + ((-2)/(x + 4)) - 1';
        $this->assertEquals($expected, $this->parameterParser->passValues($str, $values));
    }

    public function testParse(): void
    {
        $str = '((x - <par min="-5" max="5"/> + 4)/( 4 <par min="-3" max="3"/> ( 3 x + 3 ) )) + ((<par min="-4" max="4"/>)/(x + <par min="-4" max="4"/>))';
        $expected = ArrayHash::from([
            'expression' => '((x -  p0  + 4)/( 4  p1  ( 3 x + 3 ) )) + (( p2 )/(x +  p3 ))',
            'parametersCnt' => 4
        ]);
        $this->assertEquals($expected, $this->parameterParser::parse($str));
    }
}