<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.8.19
 * Time: 21:43
 */

namespace App\Model\NonPersistent\Math;

use App\Helpers\StringsHelper;
use Nette\Utils\Strings;

/**
 * Class GlobalDivider
 * @package App\Model\NonPersistent\Math
 */
class GlobalDivider
{
    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var int
     */
    protected $dividerCoefficient;

    /**
     * @var array|null
     */
    protected $dividerFactors;

    /**
     * @var array|null
     */
    protected $factors;

    /**
     * @var array|null
     */
    protected $originalFactors;

    /**
     * GlobalDivider constructor.
     * @param StringsHelper $stringsHelper
     */
    public function __construct(StringsHelper $stringsHelper)
    {
        $this->stringsHelper = $stringsHelper;
        $this->dividerCoefficient = 1;
    }

    /**
     * @param int $coefficient
     */
    public function raiseDividerCoefficient(int $coefficient): void
    {
        $this->dividerCoefficient *= $coefficient;
    }

    /**
     * @param string $factor
     */
    public function addDividerFactor(string $factor): void
    {
        if(!isset($this->dividerFactors[$factor])){
            $factor = $this->stringsHelper::trim($factor);
            $this->dividerFactors[$factor] = $factor;
        }
    }

    /**
     * @return array|string
     */
    public function getDividerFactorsString(): string
    {
        $res = '';
        foreach ($this->dividerFactors as $factor){
            $res .= $this->stringsHelper::wrap($factor);
        }
        return $res;
    }

    /**
     * @param string $factor
     */
    public function addFactor(string $factor): void
    {
        bdump('ADD FACTOR');
        if(!isset($this->factors[$factor]) && Strings::match($factor, '~p\d+~')){
            $factor = $this->stringsHelper::trim($factor);
            $this->factors[$factor] = $factor;
        }
    }

    /**
     * @return int
     */
    public function getDividerCoefficient(): int
    {
        return $this->dividerCoefficient;
    }

    /**
     * @param int $coefficient
     */
    public function setDividerCoefficient(int $coefficient): void
    {
        $this->dividerCoefficient = $coefficient;
    }

    /**
     * @return array|null
     */
    public function getDividerFactors(): ?array
    {
        return $this->dividerFactors;
    }

    /**
     * @param array|null $factors
     */
    public function setDividerFactors(?array $factors): void
    {
        $this->dividerFactors = $factors;
    }

    /**
     * @return array|null
     */
    public function getFactors(): ?array
    {
        return $this->factors;
    }

    /**
     * @param array|null $factors
     */
    public function setFactors(?array $factors): void
    {
        $this->factors = $factors;
    }

    /**
     * @return array|null
     */
    public function getOriginalFactors(): ?array
    {
        return $this->originalFactors;
    }

    /**
     * @param array|null $originalFactors
     */
    public function setOriginalFactors(?array $originalFactors): void
    {
        bdump('SET ORIGINAL FACTORS');
        bdump($originalFactors);
        $factorsCnt = count($originalFactors);
        foreach ($originalFactors as $originalFactor){
            $divider = $originalFactor[3];
            $factor = '';
            for($i = 0; $i < $factorsCnt; $i++){
                if($originalFactors[$i][3] === $divider){
                    $factor .= ($originalFactors[$i][1] . $originalFactors[$i][2]);
                }
            }
            $this->originalFactors[$factor] = $this->stringsHelper::trimOperators($factor);
        }
    }
}