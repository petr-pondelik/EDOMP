<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.4.19
 * Time: 23:49
 */

namespace App\AdminModule\Presenters;


use App\Components\DataGrids\SubCategoryGridFactory;
use App\Components\Forms\SubCategoryFormFactory;
use App\Model\Entities\SubCategory;
use App\Model\Managers\SubCategoryManager;
use App\Presenters\BasePresenter;
use App\Services\ValidationService;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\ArrayHash;

/**
 * Class SubCategoryPresenter
 * @package App\AdminModule\Presenters
 */
class SubCategoryPresenter extends AdminPresenter
{
    /**
     * @var SubCategoryManager
     */
    protected $subCategoryManager;

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
     * @param SubCategoryManager $subCategoryManager
     * @param ValidationService $validationService
     * @param SubCategoryGridFactory $subCategoryGridFactory
     * @param SubCategoryFormFactory $subCategoryFormFactory
     */
    public function __construct
    (
        SubCategoryManager $subCategoryManager,
        ValidationService $validationService,
        SubCategoryGridFactory $subCategoryGridFactory, SubCategoryFormFactory $subCategoryFormFactory
    )
    {
        parent::__construct();
        $this->subCategoryManager = $subCategoryManager;
        $this->validationService = $validationService;
        $this->subCategoryGridFactory = $subCategoryGridFactory;
        $this->subCategoryFormFactory = $subCategoryFormFactory;
    }

    /**
     * @param int $sub_category_id
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function actionEdit(int $sub_category_id)
    {
        $form = $this["subCategoryEditForm"];
        if(!$form->isSubmitted()){
            $record = $this->subCategoryManager->getById($sub_category_id);
            $this->template->subCategoryId = $sub_category_id;
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param SubCategory $record
     */
    private function setDefaults(IComponent $form, SubCategory $record)
    {
        $form["sub_category_id"]->setDefaultValue($record->sub_category_id);
        $form["sub_category_id_hidden"]->setDefaultValue($record->sub_category_id);
        $form["label"]->setDefaultValue($record->label);
        $form["category_id"]->setDefaultValue($record->category_id);
    }

    /**
     * @param $name
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentSubCategoryGrid($name)
    {
        $grid = $this->subCategoryGridFactory->create($this, $name);

        $grid->addAction("delete", "", "delete!")
            ->setIcon("trash")
            ->setClass("btn btn-danger btn-sm ajax");

        $grid->addAction("edit", "", "edit!")
            ->setIcon("edit")
            ->setClass("btn btn-primary btn-sm");

        $grid->addInlineEdit('sub_category_id')
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
     * @param int $sub_category_id
     * @throws \Dibi\Exception
     */
    public function handleDelete(int $sub_category_id)
    {
        $this->subCategoryManager->delete($sub_category_id);
        $this["subCategoryGrid"]->reload();
        $this->flashMessage("Sub-Kategorie úspěšně odstraněna.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @param int $sub_category_id
     * @throws \Nette\Application\AbortException
     */
    public function handleEdit(int $sub_category_id)
    {
        $this->redirect("SubCategory:edit", (int) $sub_category_id);
    }

    /**
     * @param int $subCategoryId
     * @param $row
     * @throws \Dibi\Exception
     */
    public function handleUpdate(int $subCategoryId, $row)
    {
        $this->subCategoryManager->update($subCategoryId, $row);
        $this->flashMessage('Sub-Kategorie úspěšně editována.', 'success');
        $this->redrawControl('mainFlashesSnippet');
    }

    /**
     * @param int $subCategoryId
     * @param $categoryId
     * @throws \Dibi\Exception
     */
    public function handleCategoryUpdate(int $subCategoryId, $categoryId)
    {
        bdump($subCategoryId);
        bdump($categoryId);
        $this->subCategoryManager->update($subCategoryId, [
            "category_id" => $categoryId
        ]);
        $this['subCategoryGrid']->reload();
        $this->flashMessage('Kategorie úspěšně změněna.', 'success');
        $this->redrawControl('mainFlashesSnippet');
    }

    /**
     * @return Form
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
     * @throws \Dibi\Exception
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values)
    {
        bdump($values);
        $this->subCategoryManager->create($values);
        $this["subCategoryGrid"]->reload();
        $this->flashMessage("Sub-kategorie úspěšně vytvořena.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @param $name
     * @return Form
     */
    public function createComponentSubCategoryEditForm($name)
    {
        $form = $this->subCategoryFormFactory->create();

        $form->addInteger("sub_category_id", "ID")
            ->setHtmlAttribute("class", "form-control")
            ->setDisabled();

        $form->addHidden("sub_category_id_hidden");

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
        $this->subCategoryManager->update($values->sub_category_id_hidden, [
            "label" => $values->label,
            "category_id" => $values->category_id
        ]);
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