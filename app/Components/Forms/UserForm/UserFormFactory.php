<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 10:38
 */

namespace App\Components\Forms\UserForm;


use App\Components\Forms\FormFactory;
use App\Model\Functionality\UserFunctionality;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\RoleRepository;
use App\Services\Validator;

/**
 * Class UserFormFactory
 * @package App\Components\Forms\UserForm
 */
class UserFormFactory extends FormFactory
{
    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * UserFormFactory constructor.
     * @param Validator $validator
     * @param UserFunctionality $userFunctionality
     * @param GroupRepository $groupRepository
     * @param RoleRepository $roleRepository
     */
    public function __construct
    (
        Validator $validator,
        UserFunctionality $userFunctionality,
        GroupRepository $groupRepository, RoleRepository $roleRepository
    )
    {
        parent::__construct($validator);
        $this->functionality = $userFunctionality;
        $this->groupRepository = $groupRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param bool $edit
     * @return UserFormControl
     */
    public function create(bool $edit = false): UserFormControl
    {
        return new UserFormControl($this->validator, $this->functionality, $this->groupRepository, $this->roleRepository, $edit);
    }
}