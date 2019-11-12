<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.10.19
 * Time: 16:45
 */

namespace App\TeacherModule\Components\Forms\TestTemplateForm;

use App\CoreModule\Arguments\ValidatorArgument;
use App\CoreModule\Components\Forms\FormControl;
use App\CoreModule\Services\FileService;
use App\CoreModule\Services\Validator;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class TestTemplateFormControl
 * @package App\TeacherModule\Components\Forms\TestTemplateForm
 */
class TestTemplateFormControl extends FormControl
{
    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * TestTemplateFormControl constructor.
     * @param Validator $validator
     * @param FileService $fileService
     */
    public function __construct
    (
        Validator $validator,
        FileService $fileService
    )
    {
        parent::__construct($validator);
        $this->fileService = $fileService;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addTextArea('templateContent', 'Obsah šablony *')
            ->setHtmlAttribute('class', 'form-control template-content hidden')
            ->setHtmlAttribute('rows', 25);

        $form->getElementPrototype()->class('form-horizontal ajax ace-editor-form');

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
        $validationFields['templateContent'] = new ValidatorArgument($values->templateContent, 'testTemplateContent');
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
        bdump($values);
        try{
            $this->fileService->updateTestTemplate($values->templateContent);
        } catch (\Exception $e) {
            bdump($e);
            $this->onError($e);
        }
        $this->onSuccess();
    }

    public function setDefaults(): void
    {
        $templateString = $this->fileService->read(TEACHER_MODULE_TEMPLATES_DIR . '/pdf/testPdf/active.latte');
        $this['form']['templateContent']->setValue($templateString);
        $this->template->templateContent = $templateString;
    }

    public function render(): void
    {
        bdump('RENDER TEST TEMPLATE FORM');
        parent::render();
    }
}