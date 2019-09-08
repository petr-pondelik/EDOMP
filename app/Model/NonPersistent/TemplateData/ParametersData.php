<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.8.19
 * Time: 18:16
 */

namespace App\Model\NonPersistent\TemplateData;

use App\Model\NonPersistent\Traits\SetValuesTrait;
use Nette\Utils\ArrayHash;

/**
 * Class ParametersData
 * @package App\Model\NonPersistent\TemplateData
 */
class ParametersData
{
    use SetValuesTrait;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var
     */
    protected $complexity;

    /**
     * @var
     */
    protected $minMax;

    /**
     * ParametersData constructor.
     * @param ArrayHash $data
     */
    public function __construct(ArrayHash $data)
    {
        //bdump('CONSTRUCT PARAMETERS DATA');
        $this->setValues($data);
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * @return mixed
     */
    public function getComplexity()
    {
        return $this->complexity;
    }

    /**
     * @param mixed $complexity
     */
    public function setComplexity($complexity): void
    {
        $this->complexity = $complexity;
    }

    /**
     * @return mixed
     */
    public function getMinMax()
    {
        return $this->minMax;
    }

    /**
     * @param mixed $minMax
     */
    public function setMinMax($minMax): void
    {
        $this->minMax = $minMax;
    }
}