<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.10.19
 * Time: 19:47
 */

namespace App\CoreModule\Components\Forms\PasswordForm;


use App\CoreModule\Arguments\ValidatorArgument;
use App\CoreModule\Components\Forms\FormControl;
use App\CoreModule\Model\Persistent\Functionality\UserFunctionality;
use App\CoreModule\Services\Validator;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class PasswordFormControl
 * @package App\CoreModule\Components\Forms\PasswordForm
 */
final class PasswordFormControl extends FormControl
{
    /**
     * @var UserFunctionality
     */
    protected $userFunctionality;

    /**
     * PasswordFormControl constructor.
     * @param Validator $validator
     * @param UserFunctionality $userFunctionality
     */
    public function __construct(Validator $validator, UserFunctionality $userFunctionality)
    {
        parent::__construct($validator);
        $this->userFunctionality = $userFunctionality;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();
        $form->addPassword('password', 'Nové heslo *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Hesla se musí shodovat a mít minimálně 8 znaků.');
        $form->addPassword('passwordConfirm', 'Potvrzení hesla *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Hesla se musí shodovat a mít minimálně 8 znaků.');
        $form->onSuccess[] = [$this, 'handleFormSuccess'];
        return $form;
    }

    /**
     * @param Form $form
     * @throws \App\CoreModule\Exceptions\ValidatorException
     */
    public function handleFormValidate(Form $form): void
    {
        bdump('HANDLE FORM VALIDATE');
        $values = $form->getValues();
        $validationFields['passwordConfirm'] = new ValidatorArgument(
            [
                'password' => $values->password,
                'passwordConfirm' => $values->passwordConfirm
            ],
            'passwordConfirm'
        );
        $this->validator->validate($form, $validationFields);
        $this->redrawErrors();
        $this->redrawFlashes();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        bdump('HANDLE FORM SUCCESS');
        try {
            $this->userFunctionality->updatePassword($this->presenter->getUser()->getId(), $values->password);
            $this->onSuccess();
        } catch (\Exception $e) {
            $this->onError($e);
        }
    }
}