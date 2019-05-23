<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 11:26
 */

namespace App\Components\Forms\CategoryForm;

use App\Components\Forms\BaseFormFactory;
use App\Model\Functionality\CategoryFunctionality;
use App\Service\ValidationService;

/**
 * Class CategoryFormFactory
 * @package App\Components\Forms\CategoryForm
 */
class CategoryFormFactory extends BaseFormFactory
{
    /**
     * @var CategoryFunctionality
     */
    protected $categoryFunctionality;

    /**
     * CategoryFormFactory constructor.
     * @param ValidationService $validationService
     * @param CategoryFunctionality $categoryFunctionality
     */
    public function __construct(ValidationService $validationService, CategoryFunctionality $categoryFunctionality)
    {
        parent::__construct($validationService);
        $this->categoryFunctionality = $categoryFunctionality;
    }

    /**
     * @param bool $edit
     * @return CategoryFormControl
     */
    public function create(bool $edit = false): CategoryFormControl
    {
        return new CategoryFormControl($this->validationService, $this->categoryFunctionality, $edit);
    }
}