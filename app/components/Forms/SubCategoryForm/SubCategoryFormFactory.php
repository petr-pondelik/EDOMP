<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 15:04
 */

namespace App\Components\Forms\SubCategoryForm;

use App\Components\Forms\BaseFormFactory;
use App\Model\Functionality\SubCategoryFunctionality;
use App\Model\Repository\CategoryRepository;
use App\Service\ValidationService;

/**
 * Class SubCategoryFormControlFactory
 * @package App\Components\Forms\SubCategoryForm
 */
class SubCategoryFormFactory extends BaseFormFactory
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * SubCategoryFormFactory constructor.
     * @param ValidationService $validationService
     * @param SubCategoryFunctionality $subCategoryFunctionality
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        ValidationService $validationService,
        SubCategoryFunctionality $subCategoryFunctionality, CategoryRepository $categoryRepository
    )
    {
        parent::__construct($validationService);
        $this->functionality = $subCategoryFunctionality;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param bool $edit
     * @return SubCategoryFormControl
     */
    public function create(bool $edit = false): SubCategoryFormControl
    {
        return new SubCategoryFormControl($this->validationService, $this->functionality, $this->categoryRepository, $edit);
    }
}