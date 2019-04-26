<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.4.19
 * Time: 22:18
 */

namespace App\AdminModule\Presenters;


use App\Components\DataGrids\GroupGridFactory;
use App\Components\Forms\GroupFormFactory;
use App\Model\Entities\Group;
use App\Model\Managers\GroupManager;
use App\Services\ValidationService;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Nette\Utils\ArrayHash;

/**
 * Class GroupPresenter
 * @package App\AdminModule\Presenters
 */
class GroupPresenter extends AdminPresenter
{
    /**
     * @var GroupManager
     */
    protected $groupManager;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * @var GroupGridFactory
     */
    protected $groupGridFactory;

    /**
     * @var GroupFormFactory
     */
    protected $groupFormFactory;

    /**
     * GroupPresenter constructor.
     * @param GroupManager $groupManager
     * @param ValidationService $validationService
     * @param GroupGridFactory $groupGridFactory
     * @param GroupFormFactory $groupFormFactory
     */
    public function __construct
    (
        GroupManager $groupManager,
        ValidationService $validationService,
        GroupGridFactory $groupGridFactory, GroupFormFactory $groupFormFactory
    )
    {
        parent::__construct();
        $this->groupManager = $groupManager;
        $this->validationService = $validationService;
        $this->groupGridFactory = $groupGridFactory;
        $this->groupFormFactory = $groupFormFactory;
    }

    /**
     * @param int $groupId
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function actionEdit(int $groupId)
    {
        $form = $this["groupEditForm"];
        if(!$form->isSubmitted()){
            $record = $this->groupManager->getById($groupId);
            $this->template->groupId = $groupId;
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param Group $record
     */
    public function setDefaults(IComponent $form, Group $record)
    {
        $form["group_id"]->setDefaultValue($record->group_id);
        $form["group_id_hidden"]->setDefaultValue($record->group_id);
        $form["label"]->setDefaultValue($record->label);
        $form["super_group_id"]->setDefaultValue($record->super_group_id);
    }

    /**
     * @param $name
     * @throws \Dibi\NotSupportedException
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentGroupGrid($name)
    {
        $grid = $this->groupGridFactory->create($this, $name);

        $grid->addAction("delete", "", "delete!")
            ->setIcon("trash")
            ->setClass("btn btn-danger btn-sm ajax");

        $grid->addAction("edit", "", "edit!")
            ->setIcon("edit")
            ->setClass("btn btn-primary btn-sm");

        $grid->addInlineEdit('group_id')
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
     * @param int $group_id
     * @throws \Dibi\Exception
     */
    public function handleDelete(int $group_id)
    {
        bdump($group_id);
        $this->groupManager->delete($group_id);
        $this["groupGrid"]->reload();
        $this->flashMessage("Skupina úspěšně odstraněna.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @param int $group_id
     * @throws \Nette\Application\AbortException
     */
    public function handleEdit(int $group_id)
    {
        $this->redirect("edit", $group_id);
    }

    /**
     * @param int $groupId
     * @param $row
     * @throws \Dibi\Exception
     */
    public function handleUpdate(int $groupId, $row)
    {
        $this->groupManager->update($groupId, $row);
        $this->flashMessage("Skupina úspěšně editována.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @param int $groupId
     * @param int $superGroupId
     * @throws \Dibi\Exception
     */
    public function handleSuperGroupUpdate(int $groupId, int $superGroupId)
    {
        $this->groupManager->update($groupId, [
            "super_group_id" => $superGroupId
        ]);
        $this["groupGrid"]->reload();
        $this->flashMessage("Super-skupina úspěšně změněna.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @return Form
     */
    public function createComponentGroupCreateForm()
    {
        $form = $this->groupFormFactory->create();
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
        $this->groupManager->create($values);
        $this->flashMessage("Skupina úspěšně vytvořena.", "success");
        $this["groupGrid"]->reload();
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @return Form
     */
    public function createComponentGroupEditForm()
    {
        $form = $this->groupFormFactory->create();
        $form->addInteger("group_id", "ID")
            ->setHtmlAttribute("class", "form-control")
            ->setDisabled();
        $form->addHidden("group_id_hidden");
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
        $this->groupManager->update($values->group_id_hidden, [
            "label" => $values->label,
            "super_group_id" => $values->super_group_id
        ]);
        $this->flashMessage("Skupina úspěšně editována.", "success");
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