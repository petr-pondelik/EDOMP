<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 31.5.19
 * Time: 21:58
 */

namespace App\Components\Forms\ProblemTypeForm;


use App\Components\Forms\FormFactory;
use App\Model\Functionality\ProblemTypeFunctionality;
use App\Services\Validator;

/**
 * Class ProblemTypeFormFactory
 * @package App\Components\Forms\ProblemTypeForm
 */
class ProblemTypeFormFactory extends FormFactory
{
    /**
     * ProblemTypeFormFactory constructor.
     * @param Validator $validator
     * @param ProblemTypeFunctionality $problemTypeFunctionality
     */
    public function __construct(Validator $validator, ProblemTypeFunctionality $problemTypeFunctionality)
    {
        parent::__construct($validator);
        $this->functionality = $problemTypeFunctionality;
    }

    /**
     * @param bool $edit
     * @return ProblemTypeFormControl
     */
    public function create(bool $edit = false): ProblemTypeFormControl
    {
        return new ProblemTypeFormControl($this->validator, $this->functionality, $edit);
    }
}