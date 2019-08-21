<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.8.19
 * Time: 0:22
 */

namespace App\Helpers;

use Nette\Utils\Strings;

/**
 * Class VariableDividers
 * @package App\Helpers
 */
class VariableDividers
{
    /**
     * @var string
     */
    protected $expression;

    /**
     * @var array
     */
    protected $matches = [];

    /**
     * @var array
     */
    protected $allVarDividers = [];

    /**
     * VariableDividers constructor.
     * @param string $expression
     * @param array $matches
     */
    public function __construct(string $expression, array $matches)
    {
        $this->expression = $expression;
        $this->matches = $matches;
        foreach ($matches as $match){
            $dividers = $this->getDividerArr($match[2]);
            foreach ($dividers as $divider){
                $divider = $this->stringsHelper::trim($divider);
                $this->addDivider($divider);
            }
        }
    }

    /**
     * @return array
     */
    public function getAllVarDividers(): array
    {
        return $this->allVarDividers;
    }

    /**
     * @return string
     */
    public function getMultiplied(): string
    {
        foreach ($this->matches as $key => $match){
            $partialDividers = $this->getDividerArr($match[2], true);
            $multipliers = '';
            foreach ($this->allVarDividers as $divider){
                if(!in_array($divider, $partialDividers, true)){
                    $multipliers .= $divider;
                }
            }
            $this->expression = Strings::replace($this->expression, $this->matches[$key][0],$this->matches[$key][1] . '*' . $multipliers);
        }
        return $this->expression;
    }

    /**
     * @param string $divider
     */
    protected function addDivider(string $divider): void
    {
        if(!isset($this->varDividers[$divider])){
            $this->allVarDividers[$divider] = $divider;
        }
    }

    /**
     * @param string $divider
     * @param bool $trim
     * @return array
     */
    protected function getDividerArr(string $divider, bool $trim = false): array
    {
        // TODO: Trim items in loop
        return explode(') (',$this->stringsHelper::trim($divider));
    }
}