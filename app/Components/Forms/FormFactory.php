<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 15:42
 */

namespace App\Components\Forms;

use App\Model\Functionality\BaseFunctionality;
use App\Services\Validator;

/**
 * Class BaseFormFactory
 * @package App\Components\Forms
 */
abstract class FormFactory
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var BaseFunctionality
     */
    protected $functionality;

    /**
     * BaseFormFactory constructor.
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }
}