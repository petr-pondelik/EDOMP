<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.4.19
 * Time: 22:18
 */

namespace App\AdminModule\Presenters;


use App\Arguments\UserInformArgs;
use App\Components\DataGrids\GroupGridFactory;
use App\Components\Forms\GroupForm\GroupFormControl;
use App\Components\Forms\GroupForm\GroupFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Model\Entity\Group;
use App\Model\Functionality\GroupFunctionality;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\SuperGroupRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use App\Services\ValidationService;
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
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param GroupRepository $groupRepository
     * @param GroupFunctionality $groupFunctionality
     * @param SuperGroupRepository $superGroupRepository
     * @param ValidationService $validationService
     * @param GroupGridFactory $groupGridFactory
     * @param GroupFormFactory $groupFormFactory
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        GroupRepository $groupRepository, GroupFunctionality $groupFunctionality, SuperGroupRepository $superGroupRepository,
        ValidationService $validationService,
        GroupGridFactory $groupGridFactory, GroupFormFactory $groupFormFactory,
        ISectionHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct($authorizator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory);
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
        $form["superGroup"]->setDefaultValue($record->getSuperGroup()->getId());
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
        try{
            $this->groupFunctionality->delete($id);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('delete', true,'error', $e));
        }
        $this["groupGrid"]->reload();
        $this->informUser(new UserInformArgs('delete', true));
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
        try{
            $this->groupFunctionality->update($id, $row);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('edit', true,'error', $e));
        }
        $this->informUser(new UserInformArgs('edit', true));
    }

    /**
     * @param int $groupId
     * @param int $superGroupId
     * @throws \Exception
     */
    public function handleSuperGroupUpdate(int $groupId, int $superGroupId): void
    {
        try{
            $this->groupFunctionality->update($groupId, ArrayHash::from([
                "superGroup" => $superGroupId
            ]));
        }
        catch (\Exception $e){
            $this->informUser(new UserInformArgs('superGroup', true, 'error', $e));
        }
        $this->informUser(new UserInformArgs('superGroup', true));
        $this["groupGrid"]->reload();
    }

    /**
     * @return GroupFormControl
     */
    public function createComponentGroupCreateForm(): GroupFormControl
    {
        $control = $this->groupFormFactory->create();
        $control->onSuccess[] = function (){
            $this["groupGrid"]->reload();
            $this->informUser(new UserInformArgs('create', true));
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('create', true,'error', $e));
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
            $this->informUser(new UserInformArgs('edit'));
            $this->redirect('default');
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('edit', false,'error', $e));
            $this->redirect('default');
        };
        return $control;
    }
}