<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 10:37
 */

namespace App\Components\Forms\UserForm;


use App\Arguments\ValidatorArgument;
use App\Components\Forms\EntityFormControl;
use App\Model\Functionality\UserFunctionality;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\RoleRepository;
use App\Services\ValidationService;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class UserFormControl
 * @package App\Components\Forms\UserForm
 */
class UserFormControl extends EntityFormControl
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

        $groupOptions = $this->groupRepository->findAllowed($this->presenter->user);
        $roleOptions = $this->roleRepository->findAllowed($this->presenter->user);

        $form->addText('username', 'Uživatelské jméno *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte login uživatele.');

        $form->addPassword('password', 'Heslo *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte heslo uživatele. (min. 8 znaků)');

        $form->addPassword('passwordConfirm', 'Potvrzení hesla *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zopakujte heslo uživatele. (min. 8 znaků)');

        $form->addText('firstName', 'Jméno')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte jméno uživatele.');

        $form->addText('lastName', 'Příjmení')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte příjmení uživatele.');

        $form->addSelect('role', 'Role *', $roleOptions)
            ->setPrompt('Zvolte roli')
            ->setHtmlAttribute('class', 'form-control');

        $form->addMultiSelect('groups', 'Skupiny *', $groupOptions)
            ->setHtmlAttribute('class', 'form-control selectpicker')
            ->setHtmlAttribute('title', 'Zvolte skupiny');

        if($this->edit){
            $form->addSelect('changePassword', 'Změnit heslo', [
                0 => 'Ne',
                1 => 'Ano'
            ])
                ->setHtmlAttribute('class', 'form-control');
        }

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->values;

        $validateFields['username'] = new ValidatorArgument([
            'username' => $values->username,
            'edit' => $this->edit,
            'userId' => $values->idHidden ?? null
        ], 'username');

//            ArrayHash::from([
//            'data' => $values->username,
//            'validation' => 'notEmpty'
//        ]);

        if(!isset($values->changePassword) || $values->changePassword){
            $validateFields['passwordConfirm'] = new ValidatorArgument([
                'password' => $values->password, 'passwordConfirm' => $values->passwordConfirm
            ],'passwordConfirm');
        }
        $validateFields['role'] = new ValidatorArgument($values->role, 'notEmpty');

//            ArrayHash::from([
//            'data' => $values->role,
//            'validation' => 'notEmpty'
//        ]);

        $validateFields['groups'] = new ValidatorArgument($values->groups, 'arrayNotEmpty');

//            ArrayHash::from([
//            'data' => $values->groups,
//            'validation' => 'arrayNotEmpty'
//        ]);

        $this->validationService->validate($form, $validateFields);

        $this->redrawErrors();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $values->userId = $this->presenter->user->id;
            $this->functionality->create($values);
            $this->onSuccess();
        } catch (\Exception $e){
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException){
                return;
            }
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
            $this->functionality->update($values->idHidden, $values);
            $this->onSuccess();
        } catch (\Exception $e){
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException){
                return;
            }
            $this->onError($e);
        }
    }

    public function render(): void
    {
        if ($this->edit){
            $this->template->render(__DIR__ . '/templates/edit.latte');
        }
        else{
            $this->template->render(__DIR__ . '/templates/create.latte');
        }
    }
}