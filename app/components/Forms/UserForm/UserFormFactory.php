<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 10:38
 */

namespace App\Components\Forms\UserForm;


use App\Components\Forms\BaseFormFactory;
use App\Model\Functionality\UserFunctionality;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\RoleRepository;
use App\Service\ValidationService;

/**
 * Class UserFormFactory
 * @package App\Components\Forms\UserForm
 */
class UserFormFactory extends BaseFormFactory
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
     * @param ValidationService $validationService
     * @param UserFunctionality $userFunctionality
     * @param GroupRepository $groupRepository
     * @param RoleRepository $roleRepository
     */
    public function __construct
    (
        ValidationService $validationService,
        UserFunctionality $userFunctionality,
        GroupRepository $groupRepository, RoleRepository $roleRepository
    )
    {
        parent::__construct($validationService);
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
        return new UserFormControl($this->validationService, $this->functionality, $this->groupRepository, $this->roleRepository, $edit);
    }
}