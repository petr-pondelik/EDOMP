<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 10:37
 */

namespace App\Components\Forms\UserForm;


use App\Components\Forms\BaseFormControl;
use App\Model\Functionality\UserFunctionality;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\RoleRepository;
use App\Service\ValidationService;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class UserFormControl
 * @package App\Components\Forms\UserForm
 */
class UserFormControl extends BaseFormControl
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
     * UserFormControl constructor.
     * @param ValidationService $validationService
     * @param UserFunctionality $userFunctionality
     * @param GroupRepository $groupRepository
     * @param RoleRepository $roleRepository
     * @param bool $edit
     */
    public function __construct
    (
        ValidationService $validationService,
        UserFunctionality $userFunctionality,
        GroupRepository $groupRepository, RoleRepository $roleRepository,
        bool $edit = false
    )
    {
        parent::__construct($validationService, $edit);
        $this->functionality = $userFunctionality;
        $this->groupRepository = $groupRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @return Form
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $groupOptions = $this->groupRepository->findAllowed();
        $roleOptions = $this->roleRepository->findAllowed($this->presenter->user->isInRole("teacher"));

        $form->addText("username", "Uživatelské jméno")
            ->setHtmlAttribute('class', 'form-control');

        $form->addPassword("password", "Heslo")
            ->setHtmlAttribute("class", "form-control");

        $form->addPassword("password_confirm", "Potvrzení hesla")
            ->setHtmlAttribute("class", "form-control");

        $form->addSelect("role", "Role", $roleOptions)
            ->setHtmlAttribute("class", "form-control");

        $form->addMultiSelect("groups", "Skupiny", $groupOptions)
            ->setHtmlAttribute("class", "form-control selectpicker");

        $form->addSubmit('submit', 'Vytvořit')
            ->setHtmlAttribute('class', 'btn btn-primary');

        if($this->edit){
            $form->addSelect("change_password", "Změnit heslo", [
                0 => "Ne",
                1 => "Ano"
            ])
                ->setHtmlAttribute("class", "form-control");
        }

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->values;

        $validateFields["username"] = $values->username;
        if(!isset($values->change_password) || $values->change_password){
            $validateFields["password_confirm"] = ArrayHash::from([
                "password" => $values->password,
                "password_confirm" => $values->password_confirm
            ]);
        }
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
        $this->redrawControl("roleErrorSnippet");
        $this->redrawControl("groupsErrorSnippet");
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $this->functionality->create($values);
            $this->onSuccess();
        } catch (\Exception $e){
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException)
                return;
            $this->onError($e);
        }
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $this->functionality->update($values->id_hidden, $values);
            $this->onSuccess();
        } catch (\Exception $e){
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException)
                return;
            $this->onError($e);
        }
    }

    public function render(): void
    {
        if ($this->edit)
            $this->template->render(__DIR__ . DIRECTORY_SEPARATOR . "edit.latte");
        else
            $this->template->render(__DIR__ . DIRECTORY_SEPARATOR . "create.latte");
    }
}