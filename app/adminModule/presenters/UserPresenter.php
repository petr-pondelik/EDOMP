<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 17.4.19
 * Time: 22:00
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\UserGridFactory;
use App\Components\Forms\UserFormFactory;
use App\Model\Entity\User;
use App\Model\Functionality\UserFunctionality;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\RoleRepository;
use App\Model\Repository\UserRepository;
use App\Service\ValidationService;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Nette\Utils\ArrayHash;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class UserPresenter
 * @package App\AdminModule\Presenters
 */
class UserPresenter extends AdminPresenter
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var UserFunctionality
     */
    protected $userFunctionality;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * @var UserGridFactory
     */
    protected $userGridFactory;

    /**
     * @var UserFormFactory
     */
    protected $userFormFactory;

    /**
     * UserPresenter constructor.
     * @param UserRepository $userRepository
     * @param UserFunctionality $userFunctionality
     * @param RoleRepository $roleRepository
     * @param GroupRepository $groupRepository
     * @param ValidationService $validationService
     * @param UserGridFactory $userGridFactory
     * @param UserFormFactory $userFormFactory
     */
    public function __construct
    (
        UserRepository $userRepository, UserFunctionality $userFunctionality,
        RoleRepository $roleRepository, GroupRepository $groupRepository,
        ValidationService $validationService,
        UserGridFactory $userGridFactory, UserFormFactory $userFormFactory
    )
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->userFunctionality = $userFunctionality;
        $this->roleRepository = $roleRepository;
        $this->groupRepository = $groupRepository;
        $this->validationService = $validationService;
        $this->userGridFactory = $userGridFactory;
        $this->userFormFactory = $userFormFactory;
    }

    /**
     * @param int $id
     * @throws \Dibi\Exception
     */
    public function actionEdit(int $id)
    {
        $form = $this["userEditForm"];
        if(!$form->isSubmitted()){
            $record = $this->userRepository->find($id);
            $this->template->id = $id;
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param User $user
     */
    public function setDefaults(IComponent $form, User $user)
    {
        $form["id"]->setDefaultValue($user->getId());
        $form["id_hidden"]->setDefaultValue($user->getId());
        $form["username"]->setDefaultValue($user->getUsername());
        $form["roles"]->setDefaultValue($user->getRolesId());
        $form["groups"]->setDefaultValue($user->getGroupsId());
    }

    /**
     * @param $name
     * @return \Ublaboo\DataGrid\DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentUserGrid($name): DataGrid
    {
        $grid = $this->userGridFactory->create($this, $name);

        $grid->addAction("delete", "", "delete!")
            ->setIcon("trash")
            ->setTitle("Odstranit uživatele")
            ->setClass("btn btn-danger btn-sm ajax");

        $grid->addAction("edit", "", "edit!")
            ->setIcon("edit")
            ->setTitle("Editovat uživatele")
            ->setClass("btn btn-primary btn-sm");

        return $grid;
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function handleDelete(int $id)
    {
        $this->userFunctionality->delete($id);
        $this["userGrid"]->reload();
        $this->flashMessage("Uživatel úspěšně odstraněn.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleEdit(int $id)
    {
        $this->redirect("edit", $id);
    }

    /**
     * @return \Nette\Application\UI\Form
     * @throws \Exception
     */
    public function createComponentUserCreateForm()
    {
        $form = $this->userFormFactory->create();
        $form->onValidate[] = [$this, "handleFormValidate"];
        $form->onSuccess[] = [$this, "handleCreateFormSuccess"];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Exception
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values)
    {
        $this->userFunctionality->create($values);
        $this["userGrid"]->reload();
        $this->flashMessage("Uživatel úspěšně vytvořen", "success");
        $this->redrawControl("mainFlashesSnippet");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentUserEditForm()
    {
        $form = $this->userFormFactory->create();
        $form->addInteger("id", "ID")
            ->setHtmlAttribute("class", "form-control")
            ->setDisabled();
        $form->addHidden("id_hidden");
        $form->addSelect("change_password", "Změnit heslo", [
            0 => "Ne",
            1 => "Ano"
        ])
            ->setHtmlAttribute("class", "form-control");
        $form->onValidate[] = [$this, "handleFormValidate"];
        $form->onSuccess[] = [$this, "handleEditFormSuccess"];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Nette\Application\AbortException
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values)
    {
        $this->userFunctionality->update($values->id_hidden, $values);
        $this->flashMessage("Uživatel úspěšně editován.", "success");
        //$this->redirect("default");
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form)
    {
        $values = $form->values;

        bdump($values);

        $validateFields["username"] = $values->username;
        if(!isset($values->change_password) || $values->change_password){
            $validateFields["password_confirm"] = ArrayHash::from([
                "password" => $values->password,
                "password_confirm" => $values->password_confirm
            ]);
        }
        $validateFields["roles"] = ArrayHash::from($values->roles);
        $validateFields["groups"] = ArrayHash::from($values->groups);

        $validationErrors = $this->validationService->validate($validateFields);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
            }
        }

        $this->redrawControl("usernameErrorSnippet");
        $this->redrawControl("passwordConfirmErrorSnippet");
        $this->redrawControl("rolesErrorSnippet");
        $this->redrawControl("groupsErrorSnippet");
    }
}