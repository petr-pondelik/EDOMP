<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.4.19
 * Time: 22:18
 */

namespace App\AdminModule\Presenters;


use App\Components\DataGrids\GroupGridFactory;
use App\Components\Forms\GroupForm\GroupFormControl;
use App\Components\Forms\GroupForm\GroupFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarFactory;
use App\Model\Entity\Group;
use App\Model\Functionality\GroupFunctionality;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\SuperGroupRepository;
use App\Service\Authorizator;
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
     * @param Authorizator $authorizator
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param GroupRepository $groupRepository
     * @param GroupFunctionality $groupFunctionality
     * @param SuperGroupRepository $superGroupRepository
     * @param ValidationService $validationService
     * @param GroupGridFactory $groupGridFactory
     * @param GroupFormFactory $groupFormFactory
     */
    public function __construct
    (
        Authorizator $authorizator,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory,
        GroupRepository $groupRepository, GroupFunctionality $groupFunctionality, SuperGroupRepository $superGroupRepository,
        ValidationService $validationService,
        GroupGridFactory $groupGridFactory, GroupFormFactory $groupFormFactory
    )
    {
        parent::__construct($authorizator, $headerBarFactory, $sideBarFactory);
        $this->groupRepository = $groupRepository;
        $this->groupFunctionality = $groupFunctionality;
        $this->superGroupRepository = $superGroupRepository;
        $this->validationService = $validationService;
        $this->groupGridFactory = $groupGridFactory;
        $this->groupFormFactory = $groupFormFactory;
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function actionEdit(int $id): void
    {
        $record = $this->groupRepository->find($id);
        if($this->user->isInRole("teacher") && !$this->authorizator->isGroupAllowed($this->user->identity, $record)){
            $this->flashMessage("Nedostatečná přístupová práva.", "danger");
            $this->redirect("Homepage:default");
        }
        $form = $this["groupEditForm"]["form"];
        if(!$form->isSubmitted()){
            $this->template->entityLabel = $record->getLabel();
            $this["groupEditForm"]->template->entityLabel = $record->getLabel();
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
     * @return GroupFormControl
     */
    public function createComponentGroupCreateForm(): GroupFormControl
    {
        $control = $this->groupFormFactory->create();
        $control->onSuccess[] = function (){
            $this["groupGrid"]->reload();
            $this->informUser('Skupina úspěšně vytvořena.', true);
        };
        $control->onError[] = function ($e){
            $this->informUser('Chyba při vytváření skupiny.', true, 'danger');
        };
        return $control;
    }

    /**
     * @return GroupFormControl
     */
    public function createComponentGroupEditForm(): GroupFormControl
    {
        $control = $this->groupFormFactory->create(true);
        $control->onSuccess[] = function (){
            $this->informUser('Skupina úspěšně editována.');
            $this->redirect('default');
        };
        $control->onError[] = function ($e){
            $this->informUser('Chyba při editaci skupiny.', false, 'danger');
            $this->redirect('default');
        };
        return $control;
    }
}