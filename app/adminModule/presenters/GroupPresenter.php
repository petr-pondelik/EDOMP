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
use App\Model\Entity\Group;
use App\Model\Functionality\GroupFunctionality;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\SuperGroupRepository;
use App\Service\ValidationService;
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
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var GroupFunctionality
     */
    protected $groupFunctionality;

    /**
     * @var SuperGroupRepository
     */
    protected $superGroupRepository;

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
     * @param GroupRepository $groupRepository
     * @param GroupFunctionality $groupFunctionality
     * @param SuperGroupRepository $superGroupRepository
     * @param ValidationService $validationService
     * @param GroupGridFactory $groupGridFactory
     * @param GroupFormFactory $groupFormFactory
     */
    public function __construct
    (
        GroupRepository $groupRepository, GroupFunctionality $groupFunctionality, SuperGroupRepository $superGroupRepository,
        ValidationService $validationService,
        GroupGridFactory $groupGridFactory, GroupFormFactory $groupFormFactory
    )
    {
        parent::__construct();
        $this->groupRepository = $groupRepository;
        $this->groupFunctionality = $groupFunctionality;
        $this->superGroupRepository = $superGroupRepository;
        $this->validationService = $validationService;
        $this->groupGridFactory = $groupGridFactory;
        $this->groupFormFactory = $groupFormFactory;
    }

    /**
     * @param int $id
     */
    public function actionEdit(int $id): void
    {
        $form = $this["groupEditForm"];
        if(!$form->isSubmitted()){
            $record = $this->groupRepository->find($id);
            $this->template->id = $id;
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param Group $record
     */
    public function setDefaults(IComponent $form, Group $record): void
    {
        $form["id"]->setDefaultValue($record->getId());
        $form["id_hidden"]->setDefaultValue($record->getId());
        $form["label"]->setDefaultValue($record->getLabel());
        $form["super_group_id"]->setDefaultValue($record->getSuperGroup()->getId());
    }

    /**
     * @param $name
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

        $grid->addInlineEdit()
            ->setIcon('pencil-alt')
            ->setTitle('Upravit inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = function($container) {
            $container->addText('label', '');
        };

        $grid->getInlineEdit()->onSetDefaults[] = function($cont, $item) {
            bdump($item);
            $cont->setDefaults([
                "label" => $item->getLabel()
            ]);
        };

        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleInlineUpdate'];
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function handleDelete(int $id): void
    {
        $this->groupFunctionality->delete($id);
        $this["groupGrid"]->reload();
        $this->flashMessage("Skupina úspěšně odstraněna.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleEdit(int $id): void
    {
        $this->redirect("edit", $id);
    }

    /**
     * @param int $id
     * @param $row
     * @throws \Exception
     */
    public function handleInlineUpdate(int $id, $row): void
    {
        $this->groupFunctionality->update($id, $row);
        $this->flashMessage("Skupina úspěšně editována.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @param int $groupId
     * @param int $superGroupId
     * @throws \Exception
     */
    public function handleSuperGroupUpdate(int $groupId, int $superGroupId): void
    {
        $this->groupFunctionality->update($groupId, ArrayHash::from([
            "super_group_id" => $superGroupId
        ]));
        $this["groupGrid"]->reload();
        $this->flashMessage("Superskupina úspěšně změněna.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentGroupCreateForm(): Form
    {
        $form = $this->groupFormFactory->create();
        $form->onValidate[] = [$this, "handleFormValidate"];
        $form->onSuccess[] = [$this, "handleCreateFormSuccess"];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Exception
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values): void
    {
        $this->groupFunctionality->create($values);
        $this->flashMessage("Skupina úspěšně vytvořena.", "success");
        $this["groupGrid"]->reload();
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentGroupEditForm(): Form
    {
        $form = $this->groupFormFactory->create();
        $form->addInteger("id", "ID")
            ->setHtmlAttribute("class", "form-control")
            ->setDisabled();
        $form->addHidden("id_hidden");
        $form->onValidate[] = [$this, "handleFormValidate"];
        $form->onSuccess[] = [$this, "handleEditFormSuccess"];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Nette\Application\AbortException
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
        $this->groupFunctionality->update($values->id_hidden, ArrayHash::from([
            "label" => $values->label,
            "super_group_id" => $values->super_group_id
        ]));
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