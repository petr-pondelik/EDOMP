<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.4.19
 * Time: 17:28
 */

namespace App\TeacherModule\Components\Forms\CategoryForm;

use App\CoreModule\Arguments\ValidatorArgument;
use App\CoreModule\Components\Forms\EntityFormControl;
use App\CoreModule\Model\Persistent\Functionality\CategoryFunctionality;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class CategoryFormControl
 * @package App\TeacherModule\Components\Forms\CategoryForm
 */
class CategoryFormControl extends EntityFormControl
{
    /**
     * CategoryFormControl constructor.
     * @param Validator $validator
     * @param ConstraintEntityManager $entityManager
     * @param CategoryFunctionality $categoryFunctionality
     */
    public function __construct
    (
        Validator $validator,
        ConstraintEntityManager $entityManager,
        CategoryFunctionality $categoryFunctionality
    )
    {
        parent::__construct($validator, $entityManager);
        $this->functionality = $categoryFunctionality;
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();
        $form->addText('label', 'Název *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte název kategorie.');
        return $form;
    }

    /**
     * @param Form $form
     * @throws \App\CoreModule\Exceptions\ValidatorException
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->values;
        $validateFields['label'] = new ValidatorArgument($values->label, 'stringNotEmpty');
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
            $values->userId = $this->presenter->user->id;
            $this->functionality->create($values);
            $this->onSuccess();
        } catch (\Exception $e) {
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
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
        try {
            $this->functionality->update($this->entity->getId(), $values);
            $this->onSuccess();
        } catch (\Exception $e) {
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException) {
                return;
            }
            $this->onError($e);
        }
    }

    public function setDefaults(): void
    {
        if(!$this->entity){
            return;
        }
        $this['form']['id']->setDefaultValue($this->entity->getId());
        $this['form']['label']->setDefaultValue($this->entity->getLabel());
    }
}