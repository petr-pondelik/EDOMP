<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 12.4.19
 * Time: 19:24
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\GroupGridFactory;
use App\Components\DataGrids\SuperGroupGridFactory;
use App\Components\Forms\PermissionForm\PermissionFormControl;
use App\Components\Forms\PermissionForm\PermissionFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarFactory;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\SuperGroupRepository;
use App\Service\Authorizator;
use Nette\ComponentModel\IComponent;

/**
 * Class SettingsPresenter
 * @package App\AdminModule\Presenters
 */
class SettingsPresenter extends AdminPresenter
{
    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var SuperGroupRepository
     */
    protected $superGroupRepository;

    /**
     * @var GroupGridFactory
     */
    protected $groupGridFactory;

    /**
     * @var SuperGroupGridFactory
     */
    protected $superGroupGridFactory;

    /**
     * @var PermissionFormFactory
     */
    protected $permissionFormFactory;

    /**
     * SettingsPresenter constructor.
     * @param Authorizator $authorizator
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param GroupRepository $groupRepository
     * @param SuperGroupRepository $superGroupRepository
     * @param GroupGridFactory $groupGridFactory
     * @param SuperGroupGridFactory $superGroupGridFactory
     * @param PermissionFormFactory $permissionFormFactory
     */
    public function __construct
    (
        Authorizator $authorizator,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory,
        GroupRepository $groupRepository, SuperGroupRepository $superGroupRepository,
        GroupGridFactory $groupGridFactory, SuperGroupGridFactory $superGroupGridFactory, PermissionFormFactory $permissionFormFactory
    )
    {
        parent::__construct($authorizator, $headerBarFactory, $sideBarFactory);
        $this->groupRepository = $groupRepository;
        $this->superGroupRepository = $superGroupRepository;
        $this->groupGridFactory = $groupGridFactory;
        $this->superGroupGridFactory = $superGroupGridFactory;
        $this->permissionFormFactory = $permissionFormFactory;
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function actionGroupPermissionEdit(int $id)
    {
        $group = $this->groupRepository->find($id);
        if($this->user->isInRole("teacher") && !$this->authorizator->isGroupAllowed($this->user->identity, $group)){
            $this->flashMessage("Nedostatečná přístupová práva.", "danger");
            $this->redirect("Homepage:default");
        }
        $form = $this['groupPermissionForm']['form'];
        if(!$form->isSubmitted()){
            $this->template->entityLabel = $group->getLabel();
            $this['groupPermissionForm']->template->entityLabel = $group->getLabel();
            $categories = $group->getCategoriesId();
            $this->setDefaults($form, $id, $categories);
        }
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function actionSuperGroupPermissionEdit(int $id)
    {
        $superGroup = $this->superGroupRepository->find($id);
        if($this->user->isInRole("teacher") && !$this->authorizator->isSuperGroupAllowed($this->user->identity, $superGroup)){
            $this->flashMessage("Nedostatečná přístupová práva.", "danger");
            $this->redirect("Homepage:default");
        }
        $form = $this["superGroupPermissionForm"]['form'];
        if(!$form->isSubmitted()){
            $this->template->entityLabel = $superGroup->getLabel();
            $this['superGroupPermissionForm']->template->entityLabel = $superGroup->getLabel();
            $categories = $superGroup->getCategoriesId();
            $this->setDefaults($form, $id, $categories);
        }
    }

    /**
     * @param IComponent $form
     * @param int $id
     * @param array $categories
     */
    public function setDefaults(IComponent $form, int $id, array $categories)
    {
        $form["id"]->setDefaultValue($id);
        $form["categories"]->setDefaultvalue($categories);
    }

    /**
     * @param $name
     * @return \Ublaboo\DataGrid\DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentGroupGrid($name)
    {
        $grid = $this->groupGridFactory->create($this, $name, true);
        $grid->addAction("editPermissions", "", "groupPermissionEdit")
            ->setIcon("key")
            ->setTitle("Nastavit oprávnění")
            ->setClass("btn btn-primary btn-sm");
        return $grid;
    }

    /**
     * @param $name
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentSuperGroupGrid($name)
    {
        $grid = $this->superGroupGridFactory->create($this, $name);
        $grid->addAction("editPermission", "", "superGroupPermissionEdit")
            ->setIcon("key")
            ->setTitle("Nastavit oprávnění")
            ->setClass("btn btn-primary btn-sm");
    }

    /**
     * @return PermissionFormControl
     */
    public function createComponentGroupPermissionForm(): PermissionFormControl
    {
        $control = $this->permissionFormFactory->create();
        $control->onSuccess[] = function (){
            $this->informUser('Oprávnění skupiny úspěšně změněna.', false);
            $this->redirect('groupPermission');
        };
        $control->onError[] = function ($e){
            $this->informUser('Chyba při změně oprávnění skupiny.', false, 'danger');
            $this->redirect('groupPermission');
        };
        return $control;
    }

    /**
     * @return PermissionFormControl
     */
    public function createComponentSuperGroupPermissionForm(): PermissionFormControl
    {
        $control = $this->permissionFormFactory->create(true);
        $control->onSuccess[] = function (){
            $this->informUser('Oprávnění superskupiny úspěšně změněna.', false);
            $this->redirect('superGroupPermission');
        };
        $control->onError[] = function ($e){
            $this->informUser('Chyba při změně oprávnění superskupiny.', false, 'danger');
            $this->redirect('superGroupPermission');
        };
        return $control;
    }
}