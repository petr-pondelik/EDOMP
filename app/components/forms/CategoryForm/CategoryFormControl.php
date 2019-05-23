<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.4.19
 * Time: 17:28
 */

namespace App\Components\Forms\CategoryForm;

use App\Components\Forms\BaseForm;
use App\Model\Functionality\CategoryFunctionality;
use App\Service\ValidationService;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class CategoryFormFactory
 * @package App\Components\Forms
 */
class CategoryFormControl extends BaseForm
{
    /**
     * @var CategoryFunctionality
     */
    protected $categoryFunctionality;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * @var bool
     */
    protected $edit;

    /**
     * @var array
     */
    public $onSuccess = [];

    /**
     * @var array
     */
    public $onError = [];

    /**
     * CategoryFormControl constructor.
     * @param ValidationService $validationService
     * @param CategoryFunctionality $categoryFunctionality
     * @param bool $edit
     */
    public function __construct
    (
        CategoryFunctionality $categoryFunctionality, ValidationService $validationService,
        bool $edit = false
    )
    {
        parent::__construct();
        $this->categoryFunctionality = $categoryFunctionality;
        $this->validationService = $validationService;
        $this->edit = $edit;
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addText('label', 'Název')
            ->setHtmlAttribute('class', 'form-control');

        $form->addSubmit('submit', 'Vytvořit')
            ->setHtmlAttribute('class', 'btn btn-primary');

        if($this->edit){
            $form->addInteger('category_id', 'ID')
                ->setHtmlAttribute('class', 'form-control')
                ->setDisabled();

            $form->addHidden('category_id_hidden');
        }

        $form->onValidate[] = [$this, 'handleFormValidate'];

        if($this->edit)
            $form->onSuccess[] = [$this, 'handleEditFormSuccess'];
        else
            $form->onSuccess[] = [$this, 'handleCreateFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form)
    {
        $values = $form->values;
        $validateFields["label"] = $values->label;
        $validationErrors = $this->validationService->validate($validateFields);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
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
        if($form->isSuccess()){
            try{
                $this->categoryFunctionality->create($values);
                $this->onSuccess();
            } catch (\Exception $e){
                //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
                if($e instanceof AbortException)
                    return;
                $this->onError($e);
            }
        }
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
        if($form->isSuccess()){
            try{
                $this->categoryFunctionality->update($values->category_id_hidden, $values);
                $this->onSuccess();
            } catch (\Exception $e){
                //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
                if($e instanceof AbortException)
                    return;
                $this->onError($e);
            }
        }
    }

    public function render(): void
    {
        if($this->edit)
            $this->template->render(__DIR__ . DIRECTORY_SEPARATOR . "categoryEditForm.latte");
        else
            $this->template->render(__DIR__ . DIRECTORY_SEPARATOR . "categoryCreateForm.latte");
    }
}