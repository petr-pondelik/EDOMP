<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 12:40
 */

namespace App\Components\Forms\PermissionForm;


use App\Components\Forms\FormFactory;
use App\Model\Persistent\Functionality\GroupFunctionality;
use App\Model\Persistent\Functionality\SuperGroupFunctionality;
use App\Model\Persistent\Repository\CategoryRepository;
use App\Services\Validator;

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
     * @param Validator $validator
     * @param GroupFunctionality $groupFunctionality
     * @param SuperGroupFunctionality $superGroupFunctionality
     * @param CategoryRepository $categoryRepository
     */
    public function __construct
    (
        Validator $validator,
        GroupFunctionality $groupFunctionality, SuperGroupFunctionality $superGroupFunctionality,
        CategoryRepository $categoryRepository
    )
    {
        parent::__construct($validator);
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
            $this->validator, $this->functionality, $this->superGroupFunctionality, $this->categoryRepository, $super
        );
    }
}