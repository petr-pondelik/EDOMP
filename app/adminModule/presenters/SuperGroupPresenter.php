<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.4.19
 * Time: 22:19
 */

namespace App\AdminModule\Presenters;


use App\Components\DataGrids\SuperGroupGridFactory;
use App\Components\Forms\SuperGroupFormFactory;
use App\Model\Entities\SuperGroup;
use App\Model\Managers\SuperGroupManager;
use App\Services\ValidationService;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Nette\Utils\ArrayHash;

/**
 * Class SuperGroupPresenter
 * @package App\AdminModule\Presenters
 */
class SuperGroupPresenter extends AdminPresenter
{
    /**
     * @var SuperGroupManager
     */
    protected $superGroupManager;

    /**
     * @var SuperGroupGridFactory
     */
    protected $superGroupGridFactory;

    /**
     * @var SuperGroupFormFactory
     */
    protected $superGroupFormFactory;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * SuperGroupPresenter constructor.
     * @param SuperGroupManager $superGroupManager
     * @param SuperGroupGridFactory $superGroupGridFactory
     * @param SuperGroupFormFactory $superGroupFormFactory
     * @param ValidationService $validationService
     */
    public function __construct
    (
        SuperGroupManager $superGroupManager,
        SuperGroupGridFactory $superGroupGridFactory, SuperGroupFormFactory $superGroupFormFactory,
        ValidationService $validationService
    )
    {
        parent::__construct();
        $this->superGroupManager = $superGroupManager;
        $this->superGroupGridFactory = $superGroupGridFactory;
        $this->superGroupFormFactory = $superGroupFormFactory;
        $this->validationService = $validationService;
    }

    /**
     * @param int $superGroupId
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function actionEdit(int $superGroupId)
    {
        $form = $this["superGroupEditForm"];
        if(!$form->isSubmitted()){
            $record = $this->superGroupManager->getById($superGroupId);
            $this->template->superGroupId = $superGroupId;
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param SuperGroup $record
     */
    private function setDefaults(IComponent $form, SuperGroup $record)
    {
        $form["super_group_id"]->setDefaultValue($record->super_group_id);
        $form["super_group_id_hidden"]->setDefaultValue($record->super_group_id);
        $form["label"]->setDefaultValue($record->label);
    }

    /**
     * @param $name
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentSuperGroupGrid($name)
    {
        $grid = $this->superGroupGridFactory->create($this, $name);

        $grid->addAction("delete", "", "delete!")
            ->setIcon("trash")
            ->setClass("btn btn-danger btn-sm ajax");

        $grid->addAction("edit", "", "edit!")
            ->setIcon("edit")
            ->setClass("btn btn-primary btn-sm");

        $grid->addInlineEdit('super_group_id')
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
     * @param int $super_group_id
     * @throws \Dibi\Exception
     */
    public function handleDelete(int $super_group_id)
    {
        $this->superGroupManager->delete($super_group_id);
        $this["superGroupGrid"]->reload();
        $this->flashMessage("Super-skupina úspěšně odstraněna.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    public function handleEdit(int $super_group_id)
    {
        $this->redirect("edit", (int) $super_group_id);
    }

    /**
     * @param int $superGroupId
     * @param $row
     * @throws \Dibi\Exception
     */
    public function handleUpdate(int $superGroupId, $row)
    {
        $this->superGroupManager->update($superGroupId, $row);
        $this->flashMessage("Super-Skupina úspěšně editována.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    public function createComponentSuperGroupCreateForm()
    {
        $form = $this->superGroupFormFactory->create();
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
        $this->superGroupManager->create($values);
        $this["superGroupGrid"]->reload();
        $this->flashMessage("Super-Skupina úspěšně vytvořena.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @return Form
     */
    public function createComponentSuperGroupEditForm()
    {
        $form = $this->superGroupFormFactory->create();
        $form->addInteger('super_group_id', 'ID')
            ->setHtmlAttribute('class', 'form-control')
            ->setDisabled();

        $form->addHidden('super_group_id_hidden');
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
        $this->superGroupManager->update($values->super_group_id_hidden, [
            "label" => $values->label
        ]);
        $this->flashMessage('Super-skupina úspěšně editována.', 'success');
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

        bdump($validationErrors);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
            }
        }

        $this->redrawControl("labelErrorSnippet");
    }

}