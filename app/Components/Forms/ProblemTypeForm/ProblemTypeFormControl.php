<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 31.5.19
 * Time: 21:54
 */

namespace App\Components\Forms\ProblemTypeForm;


use App\Arguments\ValidatorArgument;
use App\Components\Forms\EntityFormControl;
use App\Model\Persistent\Functionality\ProblemTypeFunctionality;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemTypeFormControl
 * @package App\Components\Forms\ProblemTypeForm
 */
class ProblemTypeFormControl extends EntityFormControl
{
    /**
     * ProblemTypeFormControl constructor.
     * @param Validator $validator
     * @param ConstraintEntityManager $entityManager
     * @param ProblemTypeFunctionality $problemTypeFunctionality
     */
    public function __construct
    (
        Validator $validator,
        ConstraintEntityManager $entityManager,
        ProblemTypeFunctionality $problemTypeFunctionality
    )
    {
        parent::__construct($validator, $entityManager);
        $this->functionality = $problemTypeFunctionality;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();
        $form->addText('label', 'Název *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte název typu úlohy.');
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
        try {
            $this->functionality->create($values);
            $this->onSuccess();
        } catch (\Exception $e) {
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
        try {
            $this->functionality->update($values->idHidden, $values);
            $this->onSuccess();
        } catch (\Exception $e) {
            // The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException){
                return;
            }
            $this->onError($e);
        }
    }

    public function setDefaults(): void
    {
        // TODO: Implement setDefaults() method.
    }
}