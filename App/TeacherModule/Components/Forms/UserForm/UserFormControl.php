<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 10:37
 */

namespace App\TeacherModule\Components\Forms\UserForm;


use App\CoreModule\Arguments\ValidatorArgument;
use App\CoreModule\Components\Forms\EntityFormControl;
use App\CoreModule\Model\Persistent\Functionality\UserFunctionality;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\GroupRepository;
use App\CoreModule\Model\Persistent\Repository\RoleRepository;
use App\CoreModule\Services\MailService;
use App\CoreModule\Services\PasswordGenerator;
use App\CoreModule\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class UserFormControl
 * @package App\TeacherModule\Components\Forms\UserForm
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
     * @var PasswordGenerator
     */
    protected $passwordGenerator;

    /**
     * UserFormControl constructor.
     * @param Validator $validator
     * @param ConstraintEntityManager $entityManager
     * @param MailService $mailService
     * @param UserFunctionality $userFunctionality
     * @param GroupRepository $groupRepository
     * @param RoleRepository $roleRepository
     * @param PasswordGenerator $passwordGenerator
     */
    public function __construct
    (
        Validator $validator,
        ConstraintEntityManager $entityManager,
        MailService $mailService,
        UserFunctionality $userFunctionality,
        GroupRepository $groupRepository,
        RoleRepository $roleRepository,
        PasswordGenerator $passwordGenerator
    )
    {
        parent::__construct($validator, $entityManager);
        $this->mailService = $mailService;
        $this->functionality = $userFunctionality;
        $this->groupRepository = $groupRepository;
        $this->roleRepository = $roleRepository;
        $this->passwordGenerator = $passwordGenerator;
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

        $form->addText('email', 'E-mail *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte e-mail uživatele');

        $form->addText('username', 'Uživatelské jméno')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte uživatelské jméno');

        $form->addText('firstName', 'Jméno')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte jméno uživatele');

        $form->addText('lastName', 'Příjmení')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte příjmení uživatele');

        $form->addSelect('role', 'Role *', $roleOptions)
            ->setPrompt('Zvolte roli')
            ->setHtmlAttribute('class', 'form-control');

        $form->addMultiSelect('groups', 'Skupiny *', $groupOptions)
            ->setHtmlAttribute('class', 'form-control selectpicker')
            ->setHtmlAttribute('title', 'Zvolte skupiny');

        return $form;
    }

    /**
     * @param Form $form
     * @throws \App\CoreModule\Exceptions\ValidatorException
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->values;

        // TODO: DO NOT VALIDATE USER DUPLICITY BY SELECT -> INSTEAD CATCH CONSTRAINT ERROR
        $validateFields['email'] = new ValidatorArgument($values->email, 'email');
        $validateFields['username'] = new ValidatorArgument($values->username, 'username');
        $validateFields['role'] = new ValidatorArgument($values->role, 'notEmpty');
        $validateFields['groups'] = new ValidatorArgument($values->groups, 'arrayNotEmpty');
        $validateFields['firstName'] = new ValidatorArgument($values->firstName, 'notEmpty');
        $validateFields['lastName'] = new ValidatorArgument($values->lastName, 'notEmpty');

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
        try {
            // Get ID of logged user
            $values->userId = $this->presenter->user->id;
            $values->password = $this->passwordGenerator->generate();
            // If username wasn't entered, set email as username
            if (!$values->username) {
                $values->username = $values->email;
            }
            $user = $this->functionality->create($values, false);
            $this->entityManager->flush();
            $this->mailService->sendInvitationEmail($user, $values->password);
            $this->onSuccess();
        } catch (\Exception $e) {
            bdump($e);
            // The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException) {
                return;
            }
            $this->onError($e);
        }
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleUpdateFormSuccess(Form $form, ArrayHash $values): void
    {
        bdump('HANDLE EDIT FORM SUCCESS');
        try {
            // If username wasn't entered, set email as username
            if (!$values->username) {
                $values->username = $values->email;
            }
            $this->functionality->update($this->entity->getId(), $values);
            bdump('TEST');
            $this->onSuccess();
        } catch (\Exception $e) {
            // The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException) {
                return;
            }
            $this->onError($e);
        }
    }

    public function setDefaults(): void
    {
        bdump($this->entity);
        $this['form']['id']->setDefaultValue($this->entity->getId());
        $this['form']['email']->setDefaultValue($this->entity->getEmail());
        $this['form']['username']->setDefaultValue($this->entity->getUsername());
        $this['form']['firstName']->setDefaultValue($this->entity->getFirstName());
        $this['form']['lastName']->setDefaultValue($this->entity->getLastName());
        $this['form']['role']->setDefaultValue($this->entity->getRole()->getId());
        $this['form']['groups']->setDefaultValue($this->entity->getGroupsId());
    }
}