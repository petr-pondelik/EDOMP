<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 12.4.19
 * Time: 19:24
 */

namespace App\AdminModule\Presenters;

use App\Arguments\UserInformArgs;
use App\Components\DataGrids\GroupGridFactory;
use App\Components\DataGrids\SuperGroupGridFactory;
use App\Components\Forms\PermissionForm\PermissionFormControl;
use App\Components\Forms\PermissionForm\IPermissionIFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\ISideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Model\Persistent\Repository\GroupRepository;
use App\Model\Persistent\Repository\SuperGroupRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use App\Services\Validator;
use Nette\ComponentModel\IComponent;
use Ublaboo\DataGrid\DataGrid;

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
     * @var IPermissionIFormFactory
     */
    protected $permissionFormFactory;

    /**
     * SettingsPresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param GroupRepository $groupRepository
     * @param SuperGroupRepository $superGroupRepository
     * @param GroupGridFactory $groupGridFactory
     * @param SuperGroupGridFactory $superGroupGridFactory
     * @param IPermissionIFormFactory $permissionFormFactory
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        GroupRepository $groupRepository, SuperGroupRepository $superGroupRepository,
        GroupGridFactory $groupGridFactory, SuperGroupGridFactory $superGroupGridFactory, IPermissionIFormFactory $permissionFormFactory,
        ISectionHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct($authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory);
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
    public function actionGroupPermissionEdit(int $id): void
    {
        $group = $this->groupRepository->find($id);
        if($this->user->isInRole('teacher') && !$this->authorizator->isEntityAllowed($this->user->identity, $group)){
            $this->flashMessage('Nedostatečná přístupová práva.', 'danger');
            $this->redirect('Homepage:default');
        }
        $formControl = $this['groupPermissionForm'];
        $formControl->setEntity($group);
        if(!$formControl->isSubmitted()){
            $this->template->entity = $group;
        }
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function actionSuperGroupPermissionEdit(int $id): void
    {
        $superGroup = $this->superGroupRepository->find($id);
        if($this->user->isInRole('teacher') && !$this->authorizator->isEntityAllowed($this->user->identity, $superGroup)){
            $this->flashMessage('Nedostatečná přístupová práva.', 'danger');
            $this->redirect('Homepage:default');
        }
        $formControl = $this['superGroupPermissionForm'];
        $formControl->setEntity($superGroup);
        if(!$formControl->isSubmitted()){
            $this->template->entity = $superGroup;
        }
    }

    /**
     * @param $name
     * @return DataGrid
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentGroupGrid($name): DataGrid
    {
        $grid = $this->groupGridFactory->create($this, $name, true);
        $grid->addAction('editPermissions', '', 'groupPermissionEdit')
            ->setIcon('key')
            ->setTitle('Nastavit oprávnění')
            ->setClass('btn btn-primary btn-sm');
        return $grid;
    }

    /**
     * @param $name
     * @return DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentSuperGroupGrid($name): DataGrid
    {
        $grid = $this->superGroupGridFactory->create($this, $name);
        $grid->addAction('editPermission', '', 'superGroupPermissionEdit')
            ->setIcon('key')
            ->setTitle('Nastavit oprávnění')
            ->setClass('btn btn-primary btn-sm');
        return $grid;
    }

    /**
     * @return PermissionFormControl
     */
    public function createComponentGroupPermissionForm(): PermissionFormControl
    {
        $control = $this->permissionFormFactory->create();
        $control->onSuccess[] = function (){
            $this->informUser(new UserInformArgs('groupPermissions',false));
            $this->redirect('groupPermission');
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('groupPermissions', false, 'error', $e));
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
            $this->informUser(new UserInformArgs('superGroupPermissions', false));
            $this->redirect('superGroupPermission');
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('superGroupPermissions', false, 'error', $e));
            $this->redirect('superGroupPermission');
        };
        return $control;
    }
}