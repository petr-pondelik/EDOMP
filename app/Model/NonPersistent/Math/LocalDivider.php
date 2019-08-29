<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.8.19
 * Time: 16:18
 */

namespace App\Model\NonPersistent\Math;

use Nette\Utils\Strings;

/**
 * Class LocalDivider
 * @package App\Model\NonPersistent\Math
 */
class LocalDivider
{
    /**
     * @var int
     */
    protected $coefficient;

    /**
     * @var string[]
     */
    protected $factors;

    /**
     * @var string
     */
    protected $factored;

    /**
     * LocalDivider constructor.
     */
    public function __construct()
    {
        $this->coefficient = 1;
        $this->factors = [];
    }

    /**
     * @return bool
     */
    public function isParametrized(): bool
    {
        return Strings::match($this->factored, '~p\d+~') ? true : false;
    }

    /**
     * @return int
     */
    public function getCoefficient(): int
    {
        return $this->coefficient;
    }

    /**
     * @param int $coefficient
     */
    public function setCoefficient(?int $coefficient): void
    {
        if($coefficient){
            $this->coefficient = $coefficient;
        }
    }

    /**
     * @return string[]
     */
    public function getFactors(): array
    {
        return $this->factors;
    }

    /**
     * @param string[] $factors
     */
    public function setFactors(array $factors): void
    {
        $this->factors = $factors;
    }

    /**
     * @param string $factor
     */
    public function addFactor(string $factor): void
    {
        $this->factors[] = $factor;
    }

    /**
     * @return string
     */
    public function getFactored(): string
    {
        return $this->factored;
    }

    /**
     * @param string $factored
     */
    public function setFactored(string $factored): void
    {
        $this->factored = $factored;
    }
}