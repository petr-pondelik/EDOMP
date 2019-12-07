<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.8.19
 * Time: 18:16
 */

namespace App\TeacherModule\Model\NonPersistent\TemplateData;

use App\TeacherModule\Model\NonPersistent\Traits\SetValuesTrait;
use Nette\Utils\ArrayHash;

/**
 * Class ParametersData
 * @package App\TeacherModule\Model\NonPersistent\TemplateData
 */
class ParametersData
{
    use SetValuesTrait;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var int
     */
    protected $complexity;

    /**
     * @var iterable
     */
    protected $minMax;

    /**
     * ParametersData constructor.
     * @param iterable $data
     */
    public function __construct(iterable $data)
    {
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
     * @return int
     */
    public function getComplexity(): int
    {
        return $this->complexity;
    }

    /**
     * @param int $complexity
     */
    public function setComplexity(int $complexity): void
    {
        $this->complexity = $complexity;
    }

    /**
     * @return iterable
     */
    public function getMinMax(): iterable
    {
        return $this->minMax;
    }

    /**
     * @param iterable $minMax
     */
    public function setMinMax(iterable $minMax): void
    {
        $this->minMax = $minMax;
    }
}