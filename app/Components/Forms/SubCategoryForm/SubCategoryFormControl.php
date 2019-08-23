<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 15:01
 */

namespace App\Components\Forms\SubCategoryForm;

use App\Arguments\ValidatorArgument;
use App\Components\Forms\EntityFormControl;
use App\Model\Persistent\Functionality\SubCategoryFunctionality;
use App\Model\Persistent\Repository\CategoryRepository;
use App\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class SubCategoryFormControl
 * @package App\Components\Forms\SubCategoryForm
 */
class SubCategoryFormControl extends EntityFormControl
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * SubCategoryFormControl constructor.
     * @param Validator $validator
     * @param SubCategoryFunctionality $subCategoryFunctionality
     * @param CategoryRepository $categoryRepository
     * @param bool $edit
     */
    public function __construct
    (
        Validator $validator,
        SubCategoryFunctionality $subCategoryFunctionality, CategoryRepository $categoryRepository,
        bool $edit = false
    )
    {
        parent::__construct($validator, $edit);
        $this->functionality = $subCategoryFunctionality;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $categoryOptions = $this->categoryRepository->findAssoc([], 'id');

        $form->addText('label', 'Název *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte název podkategorie.');

        $form->addSelect('category', 'Kategorie *', $categoryOptions)
            ->setPrompt('Zvolte kategorii')
            ->setHtmlAttribute('class', 'form-control');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->values;
        $validateFields['label'] = new ValidatorArgument($values->label, 'stringNotEmpty');
        $validateFields['category'] = new ValidatorArgument($values->category, 'notEmpty');
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