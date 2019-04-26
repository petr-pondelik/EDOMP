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

/**
 * Class UserFormFactory
 * @package App\Components\Forms
 */
class UserFormFactory extends BaseForm
{
    /**
     * @var GroupManager
     */
    protected $groupManager;

    /**
     * @var RoleManager
     */
    protected $roleManager;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * UserFormFactory constructor.
     * @param GroupManager $groupManager
     * @param RoleManager $roleManager
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        GroupManager $groupManager, RoleManager $roleManager,
        ConstHelper $constHelper
    )
    {
        $this->groupManager = $groupManager;
        $this->roleManager = $roleManager;
        $this->constHelper = $constHelper;
    }

    /**
     * @return \Nette\Application\UI\Form
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function create()
    {
        $form = parent::create();
        $groupOptions = $this->groupManager->getAll("ASC");
        //$roleOptions = $this->roleManager->getByCond("role_id != " . $this->constHelper::ADMIN_ROLE);
        $roleOptions = $this->roleManager->getAll();

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