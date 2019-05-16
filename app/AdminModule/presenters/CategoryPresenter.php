<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:07
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\CategoryGridFactory;
use App\Components\Forms\CategoryFormFactory;
use App\Model\Entity\Category;
use App\Model\Functionality\CategoryFunctionality;
use App\Model\Repository\CategoryRepository;
use App\Service\ValidationService;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Nette\Utils\ArrayHash;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class CategoryPresenter
 * @package App\AdminModule\Presenters
 */
class CategoryPresenter extends AdminPresenter
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var CategoryFunctionality
     */
    protected $categoryFunctionality;

    /**
     * @var CategoryGridFactory
     */
    protected $categoryGridFactory;

    /**
     * @var CategoryFormFactory
     */
    protected $categoryFormFactory;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * CategoryPresenter constructor.
     * @param CategoryRepository $categoryRepository
     * @param CategoryFunctionality $categoryFunctionality
     * @param CategoryGridFactory $categoryGridFactory
     * @param CategoryFormFactory $categoryFormFactory
     * @param ValidationService $validationService
     */
    public function __construct
    (
        CategoryRepository $categoryRepository,
        CategoryFunctionality $categoryFunctionality,
        CategoryGridFactory $categoryGridFactory, CategoryFormFactory $categoryFormFactory,
        ValidationService $validationService
    )
    {
        parent::__construct();
        $this->categoryRepository = $categoryRepository;
        $this->categoryFunctionality = $categoryFunctionality;
        $this->categoryGridFactory = $categoryGridFactory;
        $this->categoryFormFactory = $categoryFormFactory;
        $this->validationService = $validationService;
    }

    /**
     * @param int $id
     */
    public function actionEdit(int $id)
    {
        $form = $this["categoryEditForm"];
        if(!$form->isSubmitted()){
            $record = $this->categoryRepository->find($id);
            $this->template->categoryId = $id;
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param Category $record
     */
    private function setDefaults(IComponent $form, Category $record)
    {
        $form["category_id"]->setDefaultValue($record->getId());
        $form["category_id_hidden"]->setDefaultValue($record->getId());
        $form["label"]->setDefaultValue($record->getLabel());
    }

    /**
     * @param $name
     * @return DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentCategoryGrid($name): DataGrid
    {
        $grid = $this->categoryGridFactory->create($this, $name);

        $grid->addAction('delete', '', 'delete!')
            ->setIcon('trash')
            ->setClass('btn btn-danger btn-sm ajax')
            ->setTitle("Odstranit kategorii.");

        $grid->addAction("update", "", "update!")
            ->setIcon("edit")
            ->setClass("btn btn-primary btn-sm")
            ->setTitle("Editovat kategorii.");

        $grid->addInlineEdit()
            ->setIcon('pencil-alt')
            ->setTitle('Upravit inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = function($container) {
            $container->addText('label', '');
        };
        $grid->getInlineEdit()->onSetDefaults[] = function($cont, Category $item) {
            $cont->setDefaults([ "label" => $item->getLabel() ]);
        };
        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleInlineUpdate'];

        return $grid;
    }

    /**
     * @param int $id
     */
    public function handleDelete(int $id): void
    {
        try{
            $this->categoryFunctionality->delete($id);
        } catch (\Exception $e){
            $this->flashMessage('Chyba při odstraňování kategorie.', 'danger');
            $this->redrawControl('mainFlashesSnippet');
            return;
        }
        $this['categoryGrid']->reload();
        $this->flashMessage('Kategorie úspěšně odstraněna.', 'success');
        $this->redrawControl('mainFlashesSnippet');
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleUpdate(int $id): void
    {
        $this->redirect("edit", $id);
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     */
    public function handleInlineUpdate(int $id, ArrayHash $data): void
    {
        try{
            $this->categoryFunctionality->update($id, $data);
        } catch (\Exception $e){
            $this->flashMessage('Chyba při editaci kategorie.', 'danger');
            $this->redrawControl('mainFlashesSnippet');
            return;
        }
        $this->flashMessage('Kategorie úspěšně editována.', 'success');
        $this->redrawControl('mainFlashesSnippet');
    }

    /**
     * @return Form
     */
    public function createComponentCategoryCreateForm(): Form
    {
        $form = $this->categoryFormFactory->create();
        $form->onValidate[] = [$this, 'handleFormValidate'];
        $form->onSuccess[] = [$this, 'handleCreateFormSuccess'];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values)
    {
        try{
            $this->categoryFunctionality->create($values);
        } catch (\Exception $e){
            $this->flashMessage('Chyba při vytváření kategorie.', 'danger');
            $this->redrawControl('mainFlashesSnippet');
            return;
        }
        $this['categoryGrid']->reload();
        $this->flashMessage('Kategorie úspěšně vytvořena.', 'success');
        $this->redrawControl('flashesSnippet');
    }

    /**
     * @return Form
     */
    public function createComponentCategoryEditForm(): Form
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
     * @throws \Nette\Application\AbortException
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values)
    {
        try{
            $this->categoryFunctionality->update($values->category_id_hidden, $values);
        } catch (\Exception $e){
            $this->flashMessage('Chyba při editaci kategorie.', 'danger');
            $this->redirect("default");
        }
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