<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 15:04
 */

namespace App\Components\Forms\SubCategoryForm;

use App\Components\Forms\FormFactory;
use App\Model\Persistent\Functionality\SubCategoryFunctionality;
use App\Model\Persistent\Repository\CategoryRepository;
use App\Services\Validator;

/**
 * Class SubCategoryFormControlFactory
 * @package App\Components\Forms\SubCategoryForm
 */
class SubCategoryFormFactory extends FormFactory
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * SubCategoryFormFactory constructor.
     * @param Validator $validator
     * @param SubCategoryFunctionality $subCategoryFunctionality
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        Validator $validator,
        SubCategoryFunctionality $subCategoryFunctionality, CategoryRepository $categoryRepository
    )
    {
        parent::__construct($validator);
        $this->functionality = $subCategoryFunctionality;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param bool $edit
     * @return SubCategoryFormControl
     */
    public function create(bool $edit = false): SubCategoryFormControl
    {
        return new SubCategoryFormControl($this->validator, $this->functionality, $this->categoryRepository, $edit);
    }
}