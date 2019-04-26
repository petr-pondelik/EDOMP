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
use App\Model\Functionality\SubCategoryFunctionality;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Service\ValidationService;
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


    public function createComponentSubCategoryGrid($name)
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
        $this->flashMessage('Sub-Kategorie úspěšně editována.', 'success');
        $this->redrawControl('mainFlashesSnippet');
    }


    public function handleCategoryUpdate(int $subCategoryId, $categoryId)
    {
        bdump($subCategoryId);
        bdump($categoryId);
        $category = $this->categoryRepository->find($categoryId);
        $this->subCategoryFunctionality->update($subCategoryId,
            ArrayHash::from([
                "category" => $category
            ])
        );
        $this['subCategoryGrid']->reload();
        $this->flashMessage('Kategorie úspěšně změněna.', 'success');
        $this->redrawControl('mainFlashesSnippet');
    }
}