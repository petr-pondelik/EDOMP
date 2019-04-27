<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 17:19
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\SubCategoryGridFactory;
use App\Components\Forms\SubCategoryFormFactory;
use App\Model\Entity\SubCategory;
use App\Model\Functionality\SubCategoryFunctionality;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\SubCategoryRepository;
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
     * @param SubCategoryRepository $subCategoryRepository
     * @param SubCategoryFunctionality $subCategoryFunctionality
     * @param CategoryRepository $categoryRepository
     * @param ValidationService $validationService
     * @param SubCategoryGridFactory $subCategoryGridFactory
     * @param SubCategoryFormFactory $subCategoryFormFactory
     */
    public function __construct
    (
        SubCategoryRepository $subCategoryRepository, SubCategoryFunctionality $subCategoryFunctionality,
        CategoryRepository $categoryRepository,
        ValidationService $validationService,
        SubCategoryGridFactory $subCategoryGridFactory, SubCategoryFormFactory $subCategoryFormFactory
    )
    {
        parent::__construct();
        $this->subCategoryRepository = $subCategoryRepository;
        $this->subCategoryFunctionality = $subCategoryFunctionality;
        $this->categoryRepository = $categoryRepository;
        $this->validationService = $validationService;
        $this->subCategoryGridFactory = $subCategoryGridFactory;
        $this->subCategoryFormFactory = $subCategoryFormFactory;
    }

    public function actionEdit(int $id)
    {
        $form = $this["subCategoryEditForm"];
        if(!$form->isSubmitted()){
            $record = $this->subCategoryRepository->find($id);
            $this->template->subCategoryId = $id;
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param SubCategory $record
     */
    private function setDefaults(IComponent $form, SubCategory $record)
    {
        bdump($record);
        $form["sub_category"]->setDefaultValue($record->getId());
        $form["sub_category_hidden"]->setDefaultValue($record->getId());
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
            bdump($item);
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
        $category = $this->categoryRepository->find($categoryId);
        try{
            $this->subCategoryFunctionality->update($subCategoryId,
                ArrayHash::from([
                    "category" => $category
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
     * @return Form
     * @throws \Exception
     */
    public function createComponentSubCategoryCreateForm()
    {
        $form = $this->subCategoryFormFactory->create();
        $form->onValidate[] = [$this, "handleFormValidate"];
        $form->onSuccess[] = [$this, "handleCreateFormSuccess"];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Exception
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values)
    {
        try{
            $this->subCategoryFunctionality->create($values);
        } catch (\Exception $e){
            $this->flashMessage("Chyba při vytváření podkategorie.", "danger");
            $this->redrawControl("mainFlashesSnippet");
            return;
        }
        $this["subCategoryGrid"]->reload();
        $this->flashMessage("Podkategorie úspěšně vytvořena.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @param $name
     * @return Form
     * @throws \Exception
     */
    public function createComponentSubCategoryEditForm($name)
    {
        $form = $this->subCategoryFormFactory->create();

        $form->addInteger("sub_category", "ID")
            ->setHtmlAttribute("class", "form-control")
            ->setDisabled();

        $form->addHidden("sub_category_hidden");

        $form->onValidate[] = [$this, "handleFormValidate"];
        $form->onSuccess[] = [$this, "handleEditFormSuccess"];

        return $form;
    }

    public function handleEditFormSuccess(Form $form, ArrayHash $values)
    {
        bdump($values);
        $this->subCategoryFunctionality->update($values->sub_category_hidden, $values);
        $this->flashMessage('Sub-Kategorie úspěšně editována.', 'success');
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