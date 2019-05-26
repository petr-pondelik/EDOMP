<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 12:40
 */

namespace App\Components\Forms\PermissionForm;


use App\Components\Forms\FormFactory;
use App\Model\Functionality\GroupFunctionality;
use App\Model\Functionality\SuperGroupFunctionality;
use App\Model\Repository\CategoryRepository;
use App\Services\ValidationService;

/**
 * Class PermissionFormFactory
 * @package App\Components\Forms\PermissionForm
 */
class PermissionFormFactory extends FormFactory
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var SuperGroupFunctionality
     */
    protected $superGroupFunctionality;

    /**
     * PermissionFormFactory constructor.
     * @param ValidationService $validationService
     * @param GroupFunctionality $groupFunctionality
     * @param SuperGroupFunctionality $superGroupFunctionality
     * @param CategoryRepository $categoryRepository
     */
    public function __construct
    (
        ValidationService $validationService,
        GroupFunctionality $groupFunctionality, SuperGroupFunctionality $superGroupFunctionality,
        CategoryRepository $categoryRepository
    )
    {
        parent::__construct($validationService);
        $this->functionality = $groupFunctionality;
        $this->superGroupFunctionality = $superGroupFunctionality;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param bool $super
     * @return PermissionFormControl
     */
    public function create(bool $super = false): PermissionFormControl
    {
        return new PermissionFormControl(
            $this->validationService, $this->functionality, $this->superGroupFunctionality, $this->categoryRepository, $super
        );
    }
}