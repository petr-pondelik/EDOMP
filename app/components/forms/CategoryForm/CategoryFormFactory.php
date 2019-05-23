<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 11:26
 */

namespace App\Components\Forms\CategoryForm;

use App\Model\Functionality\CategoryFunctionality;
use App\Service\ValidationService;

/**
 * Class CategoryFormFactory
 * @package App\Components\Forms\CategoryForm
 */
class CategoryFormFactory
{
    /**
     * @param CategoryFunctionality $categoryFunctionality
     * @param ValidationService $validationService
     * @param bool $edit
     * @return CategoryFormControl
     */
    public function create(CategoryFunctionality $categoryFunctionality, ValidationService $validationService, bool $edit = false)
    {
        return new CategoryFormControl($categoryFunctionality, $validationService, $edit);
    }
}