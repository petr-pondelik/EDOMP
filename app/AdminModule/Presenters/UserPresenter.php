<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 17.4.19
 * Time: 22:00
 */

namespace App\AdminModule\Presenters;

use App\Arguments\UserInformArgs;
use App\Components\DataGrids\UserGridFactory;
use App\Components\Forms\UserForm\UserFormControl;
use App\Components\Forms\UserForm\UserFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Model\Entity\User;
use App\Model\Functionality\UserFunctionality;
use App\Model\Repository\UserRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use App\Services\ValidationService;
use Nette\ComponentModel\IComponent;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class UserPresenter
 * @package App\AdminModule\Presenters
 */
class UserPresenter extends AdminPresenter
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var UserFunctionality
     */
    protected $userFunctionality;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * @var UserGridFactory
     */
    protected $userGridFactory;

    /**
     * @var UserFormFactory
     */
    protected $userFormFactory;

    /**
     * UserPresenter constructor.
     * @param Authorizator $authorizator
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param UserRepository $userRepository
     * @param UserFunctionality $userFunctionality
     * @param ValidationService $validationService
     * @param UserGridFactory $userGridFactory
     * @param UserFormFactory $userFormFactory
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        UserRepository $userRepository, UserFunctionality $userFunctionality,
        ValidationService $validationService,
        UserGridFactory $userGridFactory, UserFormFactory $userFormFactory,
        ISectionHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct($authorizator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory);
        $this->userRepository = $userRepository;
        $this->userFunctionality = $userFunctionality;
        $this->validationService = $validationService;
        $this->userGridFactory = $userGridFactory;
        $this->userFormFactory = $userFormFactory;
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function actionEdit(int $id): void
    {
        $record = $this->userRepository->find($id);
        if($this->user->isInRole('teacher') && !$this->authorizator->isUserAllowed($this->user->identity, $record)){
            $this->flashMessage('Nedostatečná přístupová práva.', 'danger');
            $this->redirect('Homepage:default');
        }
        $form = $this['userEditForm']['form'];
        if(!$form->isSubmitted()){
            $this->template->entityLabel = $record->getUsername();
            $this['userEditForm']->template->entityLabel = $record->getUsername();
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param User $user
     */
    public function setDefaults(IComponent $form, User $user): void
    {
        $form['id']->setDefaultValue($user->getId());
        $form['idHidden']->setDefaultValue($user->getId());
        $form['username']->setDefaultValue($user->getUsername());
        $form['firstName']->setDefaultValue($user->getFirstName());
        $form['lastName']->setDefaultValue($user->getLastName());
        $form['role']->setDefaultValue($user->getRole()->getId());
        $form['groups']->setDefaultValue($user->getGroupsId());
    }

    /**
     * @param $name
     * @return \Ublaboo\DataGrid\DataGrid
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentUserGrid($name): DataGrid
    {
        $grid = $this->userGridFactory->create($this, $name);
        $grid->addAction('delete', '', 'delete!')
            ->setIcon('trash')
            ->setTitle('Odstranit uživatele')
            ->setClass('btn btn-danger btn-sm ajax');
        $grid->addAction('edit', '', 'edit!')
            ->setIcon('edit')
            ->setTitle('Editovat uživatele')
            ->setClass('btn btn-primary btn-sm');
        return $grid;
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function handleDelete(int $id): void
    {
        try{
            $this->userFunctionality->delete($id);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('delete', true, 'error', $e));
        }
        $this['userGrid']->reload();
        $this->informUser(new UserInformArgs('delete', true));
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleEdit(int $id): void
    {
        $this->redirect('edit', $id);
    }

    /**
     * @return UserFormControl
     */
    public function createComponentUserCreateForm(): UserFormControl
    {
        $control = $this->userFormFactory->create();
        $control->onSuccess[] = function (){
            $this['userGrid']->reload();
            $this->informUser(new UserInformArgs('create', true));
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('create', true, 'error', $e));
        };
        return $control;
    }

    /**
     * @return UserFormControl
     */
    public function createComponentUserEditForm(): UserFormControl
    {
        $control = $this->userFormFactory->create(true);
        $control->onSuccess[] = function (){
            $this['userGrid']->reload();
            $this->informUser(new UserInformArgs('edit'));
            $this->redirect('default');
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('edit', false, 'error', $e));
            $this->redirect('default');
        };
        return $control;
    }
}