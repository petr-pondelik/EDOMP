<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 11:26
 */

namespace App\Components\Forms\CategoryForm;

use App\Components\Forms\FormFactory;
use App\Model\Persistent\Functionality\CategoryFunctionality;
use App\Services\Validator;

/**
 * Class CategoryFormFactory
 * @package App\Components\Forms\CategoryForm
 */
class CategoryFormFactory extends FormFactory
{
    /**
     * CategoryFormFactory constructor.
     * @param Validator $validator
     * @param CategoryFunctionality $categoryFunctionality
     */
    public function __construct(Validator $validator, CategoryFunctionality $categoryFunctionality)
    {
        parent::__construct($validator);
        $this->functionality = $categoryFunctionality;
    }

    /**
     * @param bool $edit
     * @return CategoryFormControl
     */
    public function create(bool $edit = false): CategoryFormControl
    {
        return new CategoryFormControl($this->validator, $this->functionality, $edit);
    }
}