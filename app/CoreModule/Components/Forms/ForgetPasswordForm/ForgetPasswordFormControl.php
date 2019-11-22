<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.11.19
 * Time: 23:12
 */

namespace App\CoreModule\Components\Forms\ForgetPasswordForm;


use App\CoreModule\Arguments\ValidatorArgument;
use App\CoreModule\Components\Forms\FormControl;
use App\CoreModule\Model\Persistent\Functionality\UserFunctionality;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use App\CoreModule\Services\MailService;
use App\CoreModule\Services\PasswordGenerator;
use App\CoreModule\Services\Validator;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class ForgetPasswordFormControl
 * @package App\CoreModule\Components\Forms\ForgetPasswordForm
 */
class ForgetPasswordFormControl extends FormControl
{
    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var UserFunctionality
     */
    protected $userFunctionality;

    /**
     * @var PasswordGenerator
     */
    protected $passwordGenerator;

    /**
     * ForgetPasswordFormControl constructor.
     * @param Validator $validator
     * @param MailService $mailService
     * @param UserRepository $userRepository
     * @param PasswordGenerator $passwordGenerator
     * @param UserFunctionality $userFunctionality
     */
    public function __construct
    (
        Validator $validator,
        MailService $mailService,
        UserRepository $userRepository,
        PasswordGenerator $passwordGenerator,
        UserFunctionality $userFunctionality
    )
    {
        parent::__construct($validator);
        $this->mailService = $mailService;
        $this->userRepository = $userRepository;
        $this->passwordGenerator = $passwordGenerator;
        $this->userFunctionality = $userFunctionality;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addText('email', 'E-mail *')
            ->setHtmlAttribute('class', 'form-control');

        $form['submit']->caption = 'Zaslat nové heslo';

        $form->onSuccess[] = [$this, 'handleFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @throws \App\CoreModule\Exceptions\ValidatorException
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->getValues();
        $validationFields['email'] = new ValidatorArgument($values->email, 'email');
        $this->validator->validate($form, $validationFields);
        bdump($form->getErrors());
        $this->redrawErrors();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        $email = $values->email;
        $password = $this->passwordGenerator->generate();
        try{
            $user = $this->userFunctionality->updatePasswordByEmail($email, $password);
        } catch (EntityNotFoundException $e) {
            return;
        }
        $this->mailService->sendPasswordResetEmail($user, $password);
        $this->flashMessage('Informace byly zaslány na Váš email.', 'success');
    }
}