<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.4.19
 * Time: 23:47
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\CategoryGridFactory;
use App\Components\Forms\CategoryFormFactory;
use App\Model\Entities\Category;
use App\Model\Managers\CategoryManager;
use App\Services\ValidationService;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Nette\Utils\ArrayHash;

/**
 * Class CategoryPresenter
 * @package App\AdminModule\Presenters
 */
class CategoryPresenter extends AdminPresenter
{
    /**
     * @var CategoryManager
     */
    protected $categoryManager;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * @var CategoryGridFactory
     */
    protected $categoryGridFactory;

    /**
     * @var CategoryFormFactory
     */
    protected $categoryFormFactory;

    /**
     * CategoryPresenter constructor.
     * @param CategoryManager $categoryManager
     * @param ValidationService $validationService
     * @param CategoryGridFactory $categoryGridFactory
     * @param CategoryFormFactory $categoryFormFactory
     */
    public function __construct
    (
        CategoryManager $categoryManager,
        ValidationService $validationService,
        CategoryGridFactory $categoryGridFactory, CategoryFormFactory $categoryFormFactory
    )
    {
        parent::__construct();
        $this->validationService = $validationService;
        $this->categoryManager = $categoryManager;
        $this->categoryGridFactory = $categoryGridFactory;
        $this->categoryFormFactory = $categoryFormFactory;
    }

    /**
     * @param int $category_id
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function actionEdit(int $category_id)
    {
        $form = $this["categoryEditForm"];
        if(!$form->isSubmitted()){
            $record = $this->categoryManager->getById($category_id);
            $this->template->categoryId = $category_id;
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param Category $record
     */
    private function setDefaults(IComponent $form, Category $record)
    {
        $form["category_id"]->setDefaultValue($record->category_id);
        $form["category_id_hidden"]->setDefaultValue($record->category_id);
        $form["label"]->setDefaultValue($record->label);
    }

    /**
     * @param $name
     * @throws \Dibi\NotSupportedException
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentCategoryGrid($name)
    {
        $grid = $this->categoryGridFactory->create($this, $name);

        $grid->addAction('delete', '', 'delete!')
            ->setIcon('trash')
            ->setClass('btn btn-danger btn-sm ajax');

        $grid->addAction('edit', '')
            ->setTemplate(__DIR__ . '/templates/Category/editColumn.latte');

        $grid->addInlineEdit('category_id')
            ->setIcon('pencil-alt')
            ->setTitle('Upravit inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = function($container) {
            $container->addText('label', '');
        };

        $grid->getInlineEdit()->onSetDefaults[] = function($cont, $item) {
            bdump($item);
            $cont->setDefaults([
                "label" => $item->label
            ]);
        };

        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleUpdate'];
    }

    /**
     * @param int $category_id
     * @throws \Dibi\Exception
     */
    public function handleDelete(int $category_id)
    {
        $this->categoryManager->delete($category_id);
        $this['categoryGrid']->reload();
        $this->flashMessage('Kategorie úspěšně odstraněna', 'success');
        $this->redrawControl('mainFlashesSnippet');
    }

    /**
     * @param int $categoryId
     * @param $row
     * @throws \Dibi\Exception
     */
    public function handleUpdate(int $categoryId, $row)
    {
        $this->categoryManager->update($categoryId, $row);
        $this->flashMessage('Kategorie úspěšně editována.', 'success');
        $this->redrawControl('mainFlashesSnippet');
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    public function createComponentCategoryCreateForm()
    {
        $form = $this->categoryFormFactory->create();
        $form->onValidate[] = [$this, 'handleFormValidate'];
        $form->onSuccess[] = [$this, 'handleCreateFormSuccess'];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Dibi\Exception
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values)
    {
        bdump($values);
        $this->categoryManager->create($values);
        $this['categoryGrid']->reload();
        $this->flashMessage('Kategorie úspěšně vytvořena.', 'success');
        $this->redrawControl('flashesSnippet');
    }

    /**
     * @return Form
     */
    public function createComponentCategoryEditForm()
    {
        $form = $this->categoryFormFactory->create();
        $form->addInteger('category_id', 'ID')
            ->setHtmlAttribute('class', 'form-control')
            ->setDisabled();

        $form->addHidden('category_id_hidden');
        $form->onValidate[] = [$this, "handleFormValidate"];
        $form->onSuccess[] = [$this, "handleEditFormSuccess"];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Dibi\Exception
     * @throws \Nette\Application\AbortException
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values)
    {
        $this->categoryManager->update($values->category_id_hidden, [
                "label" => $values->label
        ]);
        $this->flashMessage('Kategorie úspěšně editována.', 'success');
        $this->redirect("default");
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
}