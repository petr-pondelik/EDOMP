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
        $res = ($this->LCMCoefficient !== 1) ? ($this->LCMCoefficient . ' *') : '';
        foreach ($this->LCMFactors as $factor){
            $res .= ' (' . $factor . ')';
        }
        return Strings::trim($res);
    }

//    /**
//     * @param string $numerator
//     */
//    public function addNumerator(string $numerator): void
//    {
//        bdump('ADD NUMERATOR');
//        if(!isset($this->numerators[$numerator]) && Strings::match($numerator, '~p\d+~')){
//            $this->numerators[$numerator] = $numerator;
//        }
//    }

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

//    /**
//     * @return array|null
//     */
//    public function getOriginalFactors(): ?array
//    {
//        return $this->originalFactors;
//    }
//
//    /**
//     * @return array|null
//     */
//    public function getNumerators(): ?array
//    {
//        return $this->numerators;
//    }
//
//    /**
//     * @param array|null $numerators
//     */
//    public function setNumerators(?array $numerators): void
//    {
//        $this->numerators = $numerators;
//    }
//
//    /**
//     * @return array
//     */
//    public function getFactoredDividers(): array
//    {
//        return $this->factoredDividers;
//    }
//
//    /**
//     * @param array $factoredDividers
//     */
//    public function setFactoredDividers(array $factoredDividers): void
//    {
//        $this->factoredDividers = $factoredDividers;
//    }
//
//    /**
//     * @param string $factoredDivider
//     */
//    public function addFactoredDivider(string $factoredDivider): void
//    {
//        if(!isset($this->factoredDividers[$factoredDivider])){
//            $this->factoredDividers[$factoredDivider] = $factoredDivider;
//        }
//    }
//
//    /**
//     * @param string $factoredDivider
//     * @return bool
//     */
//    public function hasFactoredDivider(string $factoredDivider): bool
//    {
//        return isset($this->factoredDividers[$factoredDivider]);
//    }

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