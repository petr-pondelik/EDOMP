<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.4.19
 * Time: 17:28
 */

namespace App\Components\Forms\CategoryForm;

use App\Components\Forms\EntityFormControl;
use App\Model\Functionality\CategoryFunctionality;
use App\Services\ValidationService;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class CategoryFormFactory
 * @package App\Components\Forms
 */
class CategoryFormControl extends EntityFormControl
{
    /**
     * CategoryFormControl constructor.
     * @param ValidationService $validationService
     * @param CategoryFunctionality $categoryFunctionality
     * @param bool $edit
     */
    public function __construct
    (
        ValidationService $validationService,
        CategoryFunctionality $categoryFunctionality,
        bool $edit = false
    )
    {
        parent::__construct($validationService, $edit);
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
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->values;
        $validateFields["label"] = $values->label;
        $validationErrors = $this->validationService->validate($validateFields);
        if ($validationErrors) {
            foreach ($validationErrors as $veKey => $errorGroup) {
                foreach ($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
            }
        }
        $this->redrawControl("labelErrorSnippet");
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
        try {
            $this->functionality->update($values->idHidden, $values);
            $this->onSuccess();
        } catch (\Exception $e) {
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException)
                return;
            $this->onError($e);
        }
    }

    public function render(): void
    {
        if ($this->edit)
            $this->template->render(__DIR__ . '/templates/edit.latte');
        else
            $this->template->render(__DIR__ . '/templates/create.latte');
    }
}