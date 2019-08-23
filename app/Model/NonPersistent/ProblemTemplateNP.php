<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.8.19
 * Time: 19:20
 */

namespace App\Model\NonPersistent;

use Nette\Utils\ArrayHash;

/**
 * Class ProblemTemplate
 * @package App\Model\NonPersistent
 */
abstract class ProblemTemplateNP extends BaseEntityNP
{
    /**
     * @var int
     */
    public $idHidden;

    /**
     * @var int
     */
    public $type;

    /**
     * @var int|null
     */
    public $subCategory;

    /**
     * @var string|null
     */
    public $body;

    /**
     * @var string|null
     */
    public $textBefore;

    /**
     * @var string|null
     */
    public $textAfter;

    /**
     * @var string|null
     */
    public $difficulty;

    /**
     * @var string|null
     */
    public $expression;

    /**
     * @var string|null
     */
    public $standardized;

    /**
     * @var int
     */
    public $conditionType;

    /**
     * @var int
     */
    public $accessor;

    /**
     * LinearEquationTemplate constructor.
     * @param ArrayHash $values
     */
    public function __construct(ArrayHash $values)
    {
        $this->setValues($values);
    }

    /**
     * @param ArrayHash $values
     */
    protected function setValues(ArrayHash $values): void
    {
        foreach ($values as $key => $value){
            if(property_exists(static::class, $key)){
                $this->{$key} = $value;
            }
        }
    }
}