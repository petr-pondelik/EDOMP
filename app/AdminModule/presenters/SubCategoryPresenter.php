<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 17:19
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\SubCategoryGridFactory;
use App\Components\Forms\SubCategoryForm\SubCategoryFormControl;
use App\Components\Forms\SubCategoryForm\SubCategoryFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarFactory;
use App\Model\Entity\SubCategory;
use App\Model\Functionality\SubCategoryFunctionality;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Service\Authorizator;
use App\Service\ValidationService;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Nette\Utils\ArrayHash;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class SubCategoryPresenter
 * @package App\AdminModule\Presenters
 */
class SubCategoryPresenter extends AdminPresenter
{
    /**
     * @var SubCategoryRepository
     */
    protected $subCategoryRepository;

    /**
     * @var SubCategoryFunctionality
     */
    protected $subCategoryFunctionality;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * @var SubCategoryGridFactory
     */
    protected $subCategoryGridFactory;

    /**
     * @var SubCategoryFormFactory
     */
    protected $subCategoryFormFactory;

    /**
     * SubCategoryPresenter constructor.
     * @param Authorizator $authorizator
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param SubCategoryRepository $subCategoryRepository
     * @param SubCategoryFunctionality $subCategoryFunctionality
     * @param CategoryRepository $categoryRepository
     * @param ValidationService $validationService
     * @param SubCategoryGridFactory $subCategoryGridFactory
     * @param SubCategoryFormFactory $subCategoryFormFactory
     */
    public function __construct
    (
        Authorizator $authorizator,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory,
        SubCategoryRepository $subCategoryRepository, SubCategoryFunctionality $subCategoryFunctionality,
        CategoryRepository $categoryRepository,
        ValidationService $validationService,
        SubCategoryGridFactory $subCategoryGridFactory, SubCategoryFormFactory $subCategoryFormFactory
    )
    {
        parent::__construct($authorizator, $headerBarFactory, $sideBarFactory);
        $this->subCategoryRepository = $subCategoryRepository;
        $this->subCategoryFunctionality = $subCategoryFunctionality;
        $this->categoryRepository = $categoryRepository;
        $this->validationService = $validationService;
        $this->subCategoryGridFactory = $subCategoryGridFactory;
        $this->subCategoryFormFactory = $subCategoryFormFactory;
    }

    public function actionEdit(int $id)
    {
        $form = $this["subCategoryEditForm"]["form"];
        if(!$form->isSubmitted()){
            $record = $this->subCategoryRepository->find($id);
            $this["subCategoryEditForm"]->template->subCategoryLabel = $record->getLabel();
            $this->template->entityLabel = $record->getLabel();
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param SubCategory $record
     */
    private function setDefaults(IComponent $form, SubCategory $record)
    {
        $form["id"]->setDefaultValue($record->getId());
        $form["id_hidden"]->setDefaultValue($record->getId());
        $form["label"]->setDefaultValue($record->getLabel());
        $form["category"]->setDefaultValue($record->getCategory()->getId());
    }

    /**
     * @param $name
     * @return DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentSubCategoryGrid($name): DataGrid
    {
        $grid = $this->subCategoryGridFactory->create($this, $name);

        $grid->addAction("delete", "", "delete!")
            ->setIcon("trash")
            ->setClass("btn btn-danger btn-sm ajax");

        $grid->addAction("edit", "", "edit!")
            ->setIcon("edit")
            ->setClass("btn btn-primary btn-sm");

        $grid->addInlineEdit()
            ->setIcon('pencil-alt')
            ->setTitle('Upravit inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = function($container) {
            $container->addText('label', '');
        };

        $grid->getInlineEdit()->onSetDefaults[] = function($cont, $item) {
            $cont->setDefaults([ "label" => $item->getLabel() ]);
        };

        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleUpdate'];

        return $grid;
    }

    /**
     * @param int $id
     */
    public function handleDelete(int $id)
    {
        try{
            $this->subCategoryFunctionality->delete($id);
        } catch (\Exception $e){
            $this->flashMessage("Chyba při odstaňování podkategorie.", "danger");
            $this->redrawControl("mainFlashesSnippet");
            return;
        }
        $this["subCategoryGrid"]->reload();
        $this->flashMessage("Podkategorie úspěšně odstraněna.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleEdit(int $id)
    {
        $this->redirect("SubCategory:edit", $id);
    }

    /**
     * @param int $id
     * @param $row
     */
    public function handleUpdate(int $id, $row)
    {
        try{
            $this->subCategoryFunctionality->update($id, $row);
        } catch (\Exception $e){
            $this->flashMessage('Chyba při editaci podkategorie..', 'danger');
            $this->redrawControl('mainFlashesSnippet');
            return;
        }
        $this->flashMessage('Podkategorie úspěšně editována.', 'success');
        $this->redrawControl('mainFlashesSnippet');
    }

    /**
     * @param int $subCategoryId
     * @param $categoryId
     */
    public function handleCategoryUpdate(int $subCategoryId, $categoryId)
    {
        try{
            $this->subCategoryFunctionality->update($subCategoryId,
                ArrayHash::from([
                    "category" => $categoryId
                ])
            );
        } catch (\Exception $e){
            $this->flashMessage('Chyba při změně kategorie.', 'danger');
            $this->redrawControl('mainFlashesSnippet');
        }
        $this['subCategoryGrid']->reload();
        $this->flashMessage('Kategorie úspěšně změněna.', 'success');
        $this->redrawControl('mainFlashesSnippet');
    }

    /**
     * @return SubCategoryFormControl
     */
    public function createComponentSubCategoryCreateForm(): SubCategoryFormControl
    {
        $control = $this->subCategoryFormFactory->create();
        $control->onSuccess[] = function (){
            $this["subCategoryGrid"]->reload();
            $this->informUser('Podkategorie úspěšně vytvořena.', true);
        };
        $control->onError[] = function ($e){
            $this->informUser('Chyba při vytváření podkategorie.', true, 'danger');
        };
        return $control;
    }

    /**
     * @param $name
     * @return SubCategoryFormControl
     */
    public function createComponentSubCategoryEditForm(): SubCategoryFormControl
    {
        $control = $this->subCategoryFormFactory->create(true);
        $control->onSuccess[] = function (){
            $this->informUser('Podkategorie úspěšně editována.');
            $this->redirect('default');
        };
        $control->onError[] = function ($e){
            $this->informUser('Chyba při editaci podkategorie.', false, 'danger');
            $this->redirect('default');
        };
        return $control;
    }
}