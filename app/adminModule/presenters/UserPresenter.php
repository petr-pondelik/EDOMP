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
use App\Model\Entities\User;
use App\Model\Managers\UserManager;
use App\Services\ValidationService;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Nette\Utils\ArrayHash;

/**
 * Class UserPresenter
 * @package App\AdminModule\Presenters
 */
class UserPresenter extends AdminPresenter
{
    /**
     * @var UserManager
     */
    protected $userManager;

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
     * @param UserManager $userManager
     * @param ValidationService $validationService
     * @param UserGridFactory $userGridFactory
     * @param UserFormFactory $userFormFactory
     */
    public function __construct
    (
        UserManager $userManager,
        ValidationService $validationService,
        UserGridFactory $userGridFactory, UserFormFactory $userFormFactory
    )
    {
        parent::__construct();
        $this->userManager = $userManager;
        $this->validationService = $validationService;
        $this->userGridFactory = $userGridFactory;
        $this->userFormFactory = $userFormFactory;
    }

    /**
     * @param int $userId
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function actionEdit(int $userId)
    {
        $form = $this["userEditForm"];
        if(!$form->isSubmitted()){
            $record = $this->userManager->getById($userId);
            $this->template->userId = $userId;
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param User $user
     * @throws \Dibi\Exception
     */
    public function setDefaults(IComponent $form, User $user)
    {
        $form["user_id"]->setDefaultValue($user->user_id);
        $form["user_id_hidden"]->setDefaultValue($user->user_id);
        $form["username"]->setDefaultValue($user->username);
        $form["roles"]->setDefaultValue($this->userManager->getRoles($user->user_id, true));
        $form["groups"]->setDefaultValue($this->userManager->getGroupsIds($user->user_id));
    }

    /**
     * @param $name
     * @return \Ublaboo\DataGrid\DataGrid
     * @throws \Dibi\NotSupportedException
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentUserGrid($name)
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
     * @param int $user_id
     * @throws \Dibi\Exception
     */
    public function handleDelete(int $user_id)
    {
        $this->userManager->delete($user_id);
        $this["userGrid"]->reload();
        $this->flashMessage("Uživatel úspěšně odstraněn.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    public function handleEdit(int $user_id)
    {
        $this->redirect("edit", $user_id);
    }

    /**
     * @return \Nette\Application\UI\Form
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
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
     * @throws \Dibi\Exception
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values)
    {
        $this->userManager->create($values);
        $this["userGrid"]->reload();
        $this->flashMessage("Uživatel úspěšně vytvořen", "success");
        $this->redrawControl("mainFlashesSnippet");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @return Form
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function createComponentUserEditForm()
    {
        $form = $this->userFormFactory->create();
        $form->addInteger("user_id", "ID")
            ->setHtmlAttribute("class", "form-control")
            ->setDisabled();
        $form->addHidden("user_id_hidden");
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
     * @throws \Dibi\Exception
     * @throws \Nette\Application\AbortException
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values)
    {
        $this->userManager->update($values->user_id_hidden, $values);
        $this->flashMessage("Uživatel úspěšně editován.", "success");
        $this->redirect("default");
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