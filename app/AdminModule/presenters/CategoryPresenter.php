<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:07
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\CategoryGridFactory;
use App\Components\Forms\CategoryForm\CategoryFormControl;
use App\Components\Forms\CategoryForm\CategoryFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarFactory;
use App\Model\Entity\Category;
use App\Model\Functionality\CategoryFunctionality;
use App\Model\Repository\CategoryRepository;
use App\Service\Authorizator;
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
     * CategoryPresenter constructor.
     * @param Authorizator $authorizator
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param CategoryRepository $categoryRepository
     * @param CategoryFunctionality $categoryFunctionality
     * @param CategoryGridFactory $categoryGridFactory
     * @param CategoryFormFactory $categoryFormFactory
     */
    public function __construct
    (
        Authorizator $authorizator,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory,
        CategoryRepository $categoryRepository,
        CategoryFunctionality $categoryFunctionality,
        CategoryGridFactory $categoryGridFactory, CategoryFormFactory $categoryFormFactory
    )
    {
        parent::__construct($authorizator, $headerBarFactory, $sideBarFactory);
        $this->categoryRepository = $categoryRepository;
        $this->categoryFunctionality = $categoryFunctionality;
        $this->categoryGridFactory = $categoryGridFactory;
        $this->categoryFormFactory = $categoryFormFactory;
    }

    /**
     * @param int $id
     */
    public function actionEdit(int $id)
    {
        $form = $this["categoryEditForm"]["form"];
        if(!$form->isSubmitted()){
            $record = $this->categoryRepository->find($id);
            $this["categoryEditForm"]->template->entityLabel = $record->getLabel();
            $this->template->entityLabel = $record->getLabel();
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param Category $record
     */
    private function setDefaults(IComponent $form, Category $record)
    {
        $form["id"]->setDefaultValue($record->getId());
        $form["id_hidden"]->setDefaultValue($record->getId());
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
            $this->informUser('Chyba při odstraňování kategorie.', true,'danger');
            return;
        }
        $this['categoryGrid']->reload();
        $this->informUser('Kategorie úspěšně odstraněna.', true);
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
            $this->informUser('Chyba při editaci kategorie.', true,'danger');
            return;
        }
        $this->informUser('Kategorie úspěšně editována.', true, 'success');
    }

    /**
     * @return CategoryFormControl
     */
    public function createComponentCategoryCreateForm(): CategoryFormControl
    {
        $control = $this->categoryFormFactory->create();
        $control->onSuccess[] = function (){
            $this['categoryGrid']->reload();
            $this->informUser('Kategorie úspěšně vytvořena.', true);
        };
        $control->onError[] = function ($e){
            $this->informUser('Chyba při vytváření kategorie.', true, 'danger');
        };
        return $control;
    }

    /**
     * @return CategoryFormControl
     */
    public function createComponentCategoryEditForm(): CategoryFormControl
    {
        $control = $this->categoryFormFactory->create(true);
        $control->onSuccess[] = function (){
            $this->informUser('Kategorie úspěšně editována.');
            $this->redirect('default');
        };
        $control->onError[] = function ($e){
            $this->informUser('Chyba při editaci kategorie.', false, 'danger');
            $this->redirect('default');
        };
        return $control;
    }

}