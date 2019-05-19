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
     * @param $container
     * @return \Nette\Application\UI\Form
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function create($container = null)
    {
        $form = parent::create();
        $groupOptions = $this->groupRepository->findWithoutAdmin();
        $roleOptions = $this->roleRepository->findWithoutAdmin($container->user->isInRole("teacher"));

        $form->addText("username", "Uživatelské jméno")
            ->setHtmlAttribute('class', 'form-control');

        $form->addPassword("password", "Heslo")
            ->setHtmlAttribute("class", "form-control");

        $form->addPassword("password_confirm", "Potvrzení hesla")
            ->setHtmlAttribute("class", "form-control");

        bdump($roleOptions);

        $form->addSelect("role", "Role", $roleOptions)
            ->setHtmlAttribute("class", "form-control");

        $form->addMultiSelect("groups", "Skupiny", $groupOptions)
            ->setHtmlAttribute("class", "form-control selectpicker");

        $form->addSubmit('submit', 'Vytvořit')
            ->setHtmlAttribute('class', 'btn btn-primary');

        return $form;
    }
}