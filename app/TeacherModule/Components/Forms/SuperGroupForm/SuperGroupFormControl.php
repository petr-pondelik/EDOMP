<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 18:43
 */

namespace App\Components\Forms\SuperGroupForm;


use App\CoreModule\Arguments\ValidatorArgument;
use App\CoreModule\Components\Forms\EntityFormControl;
use App\Model\Persistent\Functionality\SuperGroupFunctionality;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class SuperGroupFormControl
 * @package App\Components\Forms\SuperGroupForm
 */
class SuperGroupFormControl extends EntityFormControl
{
    /**
     * SuperGroupFormControl constructor.
     * @param Validator $validator
     * @param ConstraintEntityManager $entityManager
     * @param SuperGroupFunctionality $superGroupFunctionality
     */
    public function __construct
    (
        Validator $validator,
        ConstraintEntityManager $entityManager,
        SuperGroupFunctionality $superGroupFunctionality
    )
    {
        parent::__construct($validator, $entityManager);
        $this->functionality = $superGroupFunctionality;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addText('label', 'Název *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte název superskupiny.');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->values;
        $validateFields['label'] = new ValidatorArgument($values->label, 'stringNotEmpty');
        $this->validator->validate($form, $validateFields);
        $this->redrawErrors();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $values->user_id = $this->presenter->user->id;
            $this->functionality->create($values);
            $this->onSuccess();
        } catch (\Exception $e){
            // The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if($e instanceof AbortException){
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
            $this->functionality->update($this->entity->getId(), ArrayHash::from([ 'label' => $values->label ]));
            $this->onSuccess();
        } catch (\Exception $e){
            // The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if($e instanceof AbortException){
                return;
            }
            $this->onError($e);
        }
    }

    public function setDefaults(): void
    {
        $this['form']['id']->setDefaultValue($this->entity->getId());
        $this['form']['label']->setDefaultValue($this->entity->getLabel());
    }
}