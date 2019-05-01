<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 18.4.19
 * Time: 10:24
 */

namespace App\Components\Forms;

use App\Helpers\ConstHelper;
use App\Model\Managers\GroupManager;
use App\Model\Managers\RoleManager;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\RoleRepository;

/**
 * Class UserFormFactory
 * @package App\Components\Forms
 */
class UserFormFactory extends BaseForm
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
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * UserFormFactory constructor.
     * @param GroupRepository $groupRepository
     * @param RoleRepository $roleRepository
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        GroupRepository $groupRepository, RoleRepository $roleRepository,
        ConstHelper $constHelper
    )
    {
        $this->groupRepository = $groupRepository;
        $this->roleRepository = $roleRepository;
        $this->constHelper = $constHelper;
    }

    /**
     * @return \Nette\Application\UI\Form
     * @throws \Exception
     */
    public function create()
    {
        $form = parent::create();
        $groupOptions = $this->groupRepository->findAssoc([], "id");
        $roleOptions = $this->roleRepository->findWithoutAdmin();

        $form->addText("username", "Uživatelské jméno")
            ->setHtmlAttribute('class', 'form-control');

        $form->addPassword("password", "Heslo")
            ->setHtmlAttribute("class", "form-control");

        $form->addPassword("password_confirm", "Potvrzení hesla")
            ->setHtmlAttribute("class", "form-control");

        bdump($roleOptions);

        $form->addMultiSelect("roles", "Role", $roleOptions)
            ->setHtmlAttribute("class", "form-control selectpicker");

        $form->addMultiSelect("groups", "Skupiny", $groupOptions)
            ->setHtmlAttribute("class", "form-control selectpicker");

        $form->addSubmit('submit', 'Vytvořit')
            ->setHtmlAttribute('class', 'btn btn-primary');

        return $form;
    }
}