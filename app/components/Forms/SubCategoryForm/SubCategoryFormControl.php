<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 15:01
 */

namespace App\Components\Forms\SubCategoryForm;

use App\Components\Forms\BaseFormControl;
use App\Model\Functionality\SubCategoryFunctionality;
use App\Model\Repository\CategoryRepository;
use App\Service\ValidationService;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class SubCategoryFormControl
 * @package App\Components\Forms\SubCategoryForm
 */
class SubCategoryFormControl extends BaseFormControl
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * SubCategoryFormControl constructor.
     * @param ValidationService $validationService
     * @param SubCategoryFunctionality $subCategoryFunctionality
     * @param CategoryRepository $categoryRepository
     * @param bool $edit
     */
    public function __construct
    (
        ValidationService $validationService,
        SubCategoryFunctionality $subCategoryFunctionality, CategoryRepository $categoryRepository,
        bool $edit = false
    )
    {
        parent::__construct($validationService, $edit);
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

        $categoryOptions = $this->categoryRepository->findAssoc([], "id");

        $form->addText("label", "Název")
            ->setHtmlAttribute("class", "form-control");

        $form->addSelect("category_id", "Kategorie", $categoryOptions)
            ->setHtmlAttribute("class", "form-control");

        $form->addSubmit("submit", "Vytvořit")
            ->setHtmlAttribute("class", "btn btn-primary");

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
    public function handleCreateFormSuccess(Form $form, ArrayHash $values): void
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
            $this->functionality->update($values->id_hidden, $values);
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
            $this->template->render(__DIR__ . DIRECTORY_SEPARATOR . "edit.latte");
        else
            $this->template->render(__DIR__ . DIRECTORY_SEPARATOR . "create.latte");
    }
}