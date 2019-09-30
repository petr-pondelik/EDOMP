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
use App\Model\Persistent\Functionality\UserFunctionality;
use App\Model\Persistent\Repository\GroupRepository;
use App\Model\Persistent\Repository\RoleRepository;
use App\Services\MailService;
use App\Services\Validator;
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
     * @var MailService
     */
    protected $mailService;

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
     * @param Validator $validator
     * @param MailService $mailService
     * @param UserFunctionality $userFunctionality
     * @param GroupRepository $groupRepository
     * @param RoleRepository $roleRepository
     */
    public function __construct
    (
        Validator $validator,
        MailService $mailService,
        UserFunctionality $userFunctionality,
        GroupRepository $groupRepository,
        RoleRepository $roleRepository
    )
    {
        parent::__construct($validator);
        $this->mailService = $mailService;
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

        if($this->isUpdate()){
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

        // TODO: DO NOT VALIDATE USER DUPLICITY BY SELECT -> INSTEAD CATCH CONSTRAINT ERROR
        $validateFields['username'] = new ValidatorArgument([
            'username' => $values->username,
            'edit' => $this->isUpdate(),
            'userId' => $this->entity ? $this->entity->getId() : null
        ], 'username');

        if(!isset($values->changePassword) || $values->changePassword){
            $validateFields['passwordConfirm'] = new ValidatorArgument([
                'password' => $values->password, 'passwordConfirm' => $values->passwordConfirm
            ],'passwordConfirm');
        }

        $validateFields['role'] = new ValidatorArgument($values->role, 'notEmpty');
        $validateFields['groups'] = new ValidatorArgument($values->groups, 'arrayNotEmpty');

        $this->validator->validate($form, $validateFields);

        $this->redrawErrors();
        $this->redrawFlashes();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            // Get ID of logged user
            $values->userId = $this->presenter->user->id;
            $values->password = 'TESTPASSWORD';
            $user = $this->functionality->create($values);
            $user->setPassword($values->password, false);
            $this->mailService->sendInvitationEmail($user);
            $this->onSuccess();
        } catch (\Exception $e){
            // The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
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
            $this->functionality->update($this->entity->getId(), $values);
            $this->onSuccess();
        } catch (\Exception $e){
            // The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException){
                return;
            }
            $this->onError($e);
        }
    }

    public function setDefaults(): void
    {
        $this['form']['id']->setDefaultValue($this->entity->getId());
        $this['form']['username']->setDefaultValue($this->entity->getUsername());
        $this['form']['firstName']->setDefaultValue($this->entity->getFirstName());
        $this['form']['lastName']->setDefaultValue($this->entity->getLastName());
        $this['form']['role']->setDefaultValue($this->entity->getRole()->getId());
        $this['form']['groups']->setDefaultValue($this->entity->getGroupsId());
    }
}