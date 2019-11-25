<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 12.4.19
 * Time: 19:24
 */

namespace App\TeacherModule\Presenters;

use App\CoreModule\Arguments\UserInformArgs;
use App\TeacherModule\Components\DataGrids\GroupGridFactory;
use App\TeacherModule\Components\DataGrids\SuperGroupGridFactory;
use App\CoreModule\Components\Forms\PasswordForm\IPasswordFormFactory;
use App\CoreModule\Components\Forms\PasswordForm\PasswordFormControl;
use App\TeacherModule\Components\Forms\PermissionForm\PermissionFormControl;
use App\TeacherModule\Components\Forms\PermissionForm\IPermissionFormFactory;
use App\TeacherModule\Components\Forms\TestTemplateForm\ITestTemplateFormFactory;
use App\TeacherModule\Components\Forms\TestTemplateForm\TestTemplateFormControl;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Repository\GroupRepository;
use App\CoreModule\Model\Persistent\Repository\SuperGroupRepository;
use App\CoreModule\Services\Authorizator;
use App\CoreModule\Services\FileService;
use App\TeacherModule\Services\NewtonApiClient;
use App\CoreModule\Services\Validator;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class SettingsPresenter
 * @package App\TeacherModule\Presenters
 */
class SettingsPresenter extends TeacherPresenter
{
    /**
     * @var FileService
     */
    protected $fileService;

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
     * @var IPermissionFormFactory
     */
    protected $permissionFormFactory;

    /**
     * @var IPasswordFormFactory
     */
    protected $passwordFormFactory;

    /**
     * @var ITestTemplateFormFactory
     */
    protected $testTemplateFormFactory;

    /**
     * SettingsPresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param FileService $fileService
     * @param GroupRepository $groupRepository
     * @param SuperGroupRepository $superGroupRepository
     * @param GroupGridFactory $groupGridFactory
     * @param SuperGroupGridFactory $superGroupGridFactory
     * @param IPermissionFormFactory $permissionFormFactory
     * @param IHelpModalFactory $sectionHelpModalFactory
     * @param IPasswordFormFactory $passwordFormFactory
     * @param ITestTemplateFormFactory $testTemplateFormFactory
     */
    public function __construct
    (
        Authorizator $authorizator,
        Validator $validator,
        NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory,
        ISideBarFactory $sideBarFactory,
        FlashesTranslator $flashesTranslator,
        FileService $fileService,
        GroupRepository $groupRepository,
        SuperGroupRepository $superGroupRepository,
        GroupGridFactory $groupGridFactory,
        SuperGroupGridFactory $superGroupGridFactory,
        IPermissionFormFactory $permissionFormFactory,
        IHelpModalFactory $sectionHelpModalFactory,
        IPasswordFormFactory $passwordFormFactory,
        ITestTemplateFormFactory $testTemplateFormFactory
    )
    {
        parent::__construct($authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory);
        $this->fileService = $fileService;
        $this->groupRepository = $groupRepository;
        $this->superGroupRepository = $superGroupRepository;
        $this->groupGridFactory = $groupGridFactory;
        $this->superGroupGridFactory = $superGroupGridFactory;
        $this->permissionFormFactory = $permissionFormFactory;
        $this->passwordFormFactory = $passwordFormFactory;
        $this->testTemplateFormFactory = $testTemplateFormFactory;
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function actionGroupPermissionEdit(int $id): void
    {
        $group = $this->groupRepository->find($id);

        if (!$group) {
            $this->flashMessage('Entita nenalezena.', 'danger');
            $this->redirect('groupPermission');
        }

        if($this->user->isInRole('teacher') && !$this->authorizator->isEntityAllowed($this->user, $group)){
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

        if (!$superGroup) {
            $this->flashMessage('Entita nenalezena.', 'danger');
            $this->redirect('superGroupPermission');
        }

        if($this->user->isInRole('teacher') && !$this->authorizator->isEntityAllowed($this->user, $superGroup)){
            $this->flashMessage('Nedostatečná přístupová práva.', 'danger');
            $this->redirect('Homepage:default');
        }
        $formControl = $this['superGroupPermissionForm'];
        $formControl->setEntity($superGroup);
        if(!$formControl->isSubmitted()){
            $this->template->entity = $superGroup;
        }
    }

    public function actionTestTemplate(): void
    {
        $formControl = $this['testTemplateForm'];
        if(!$formControl->isSubmitted()){
            $formControl->setDefaults();
        }
    }

    public function handleResetTestTemplate(): void
    {
        bdump('RESET TEST TEMPLATE');
        $this->fileService->resetTestTemplate();
        $control = $this['testTemplateForm'];
        $control->flashMessage('Výchozí šablona testu byla obnovena.', 'success');
        $control->setDefaults();
        $control->redrawControl();
        $this->redrawControl('adminScriptsSnippet');
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
     * @throws \Doctrine\ORM\Query\QueryException
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
            $this->informUser(new UserInformArgs('groupPermissions',true, 'success', null, false, 'groupPermissionForm'));
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('groupPermissions', true, 'error', $e, false, 'groupPermissionForm'));
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
            $this->informUser(new UserInformArgs('superGroupPermissions', true, 'success', null, false, 'superGroupPermissionForm'));
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('superGroupPermissions', true, 'error', $e, false, 'superGroupPermissionForm'));
        };
        return $control;
    }

    /**
     * @return PasswordFormControl
     */
    public function createComponentPasswordForm(): PasswordFormControl
    {
        $control = $this->passwordFormFactory->create();
        $control->onSuccess[] = function () {
            $this->informUser(new UserInformArgs($this->getAction(), true, 'success', null,false,'passwordForm'));
        };
        $control->onError[] = function (\Exception $e) {
            $this->informUser(new UserInformArgs($this->getAction(), true, 'danger', $e, false, 'passwordForm'));
        };
        return $control;
    }

    /**
     * @return TestTemplateFormControl
     */
    public function createComponentTestTemplateForm(): TestTemplateFormControl
    {
        $control = $this->testTemplateFormFactory->create();
        $control->onSuccess[] = function () {
            $this->informUser(new UserInformArgs($this->getAction(), true, 'success', null, false, 'testTemplateForm'));
        };
        $control->onError[] = function ($e) {
            $this->informUser(new UserInformArgs($this->getAction(), true, 'danger', $e, false, 'testTemplateForm'));
        };
        return $control;
    }
}