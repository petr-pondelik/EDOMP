<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.9.19
 * Time: 15:21
 */

namespace App\TeacherModule\Model\NonPersistent\Generator;

/**
 * Class VariantLabel
 * @package App\TeacherModule\Model\NonPersistent\Generator
 */
class Variant
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $label;

    /**
     * Variant constructor.
     * @param int $id
     * @param string $label
     */
    public function __construct(int $id, string $label)
    {
        $this->id = $id;
        $this->label = $label;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }
}