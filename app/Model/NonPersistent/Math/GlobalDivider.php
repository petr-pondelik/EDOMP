<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.8.19
 * Time: 21:43
 */

namespace App\Model\NonPersistent\Math;

use Nette\Utils\Strings;

/**
 * Class GlobalDivider
 * @package App\Model\NonPersistent\Math
 */
class GlobalDivider
{
    /**
     * @var string|null
     */
    protected $LCMExpression;

    /**
     * @var int
     */
    protected $LCMCoefficient;

    /**
     * @var array
     */
    protected $LCMFactors;

    /**
     * GlobalDivider constructor.
     */
    public function __construct()
    {
        $this->LCMCoefficient = 1;
        $this->LCMFactors = [];
    }

    /**
     * @param int $coefficient
     */
    public function raiseLCMCoefficient(int $coefficient): void
    {
        $this->LCMCoefficient *= $coefficient;
    }

    /**
     * @param int $coefficient
     */
    public function reduceLCMCoefficient(int $coefficient): void
    {
        bdump('REDUCE LCM COEFFICIENT');
        bdump($coefficient);
        $this->LCMCoefficient /= $coefficient;
    }

    /**
     * @param string $factor
     */
    public function addLCMFactor(string $factor): void
    {
        if(!isset($this->dividerFactors[$factor])){
            $this->LCMFactors[$factor] = $factor;
        }
    }

    /**
     * @return array|string
     */
    public function getLCMString(): string
    {
        $res = '';
        foreach ($this->LCMFactors as $factor){
            $res .= ' (' . $factor . ')';
        }
        return '(' . $res . ')';
    }

    /**
     * @return int
     */
    public function getLCMCoefficient(): int
    {
        return $this->LCMCoefficient;
    }

    /**
     * @param int $coefficient
     */
    public function setLCMCoefficient(int $coefficient): void
    {
        $this->LCMCoefficient = $coefficient;
    }

    /**
     * @return array|null
     */
    public function getLCMFactors(): ?array
    {
        return $this->LCMFactors;
    }

    /**
     * @param array|null $lcmFactors
     */
    public function setLCMFactors(?array $lcmFactors): void
    {
        $this->LCMFactors = $lcmFactors;
    }

    /**
     * @return string|null
     */
    public function getLCMExpression(): ?string
    {
        return $this->LCMExpression;
    }

    /**
     * @param string|null $LCMExpression
     */
    public function setLCMExpression(?string $LCMExpression): void
    {
        $this->LCMExpression = $LCMExpression;
    }
}