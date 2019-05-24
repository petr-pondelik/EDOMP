<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 17.4.19
 * Time: 22:00
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\UserGridFactory;
use App\Components\Forms\UserForm\UserFormControl;
use App\Components\Forms\UserForm\UserFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarFactory;
use App\Model\Entity\User;
use App\Model\Functionality\UserFunctionality;
use App\Model\Repository\UserRepository;
use App\Service\Authorizator;
use App\Service\ValidationService;
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
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param UserRepository $userRepository
     * @param UserFunctionality $userFunctionality
     * @param ValidationService $validationService
     * @param UserGridFactory $userGridFactory
     * @param UserFormFactory $userFormFactory
     */
    public function __construct
    (
        Authorizator $authorizator,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory,
        UserRepository $userRepository, UserFunctionality $userFunctionality,
        ValidationService $validationService,
        UserGridFactory $userGridFactory, UserFormFactory $userFormFactory
    )
    {
        parent::__construct($authorizator, $headerBarFactory, $sideBarFactory);
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
    public function actionEdit(int $id)
    {
        $record = $this->userRepository->find($id);
        if($this->user->isInRole("teacher") && !$this->authorizator->isUserAllowed($this->user->identity, $record)){
            $this->flashMessage("Nedostatečná přístupová práva.", "danger");
            $this->redirect("Homepage:default");
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
    public function setDefaults(IComponent $form, User $user)
    {
        $form["id"]->setDefaultValue($user->getId());
        $form["id_hidden"]->setDefaultValue($user->getId());
        $form["username"]->setDefaultValue($user->getUsername());
        $form["role"]->setDefaultValue($user->getRole()->getId());
        $form["groups"]->setDefaultValue($user->getGroupsId());
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
        $grid->addAction("delete", "", "delete!")
            ->setIcon("trash")
            ->setTitle("Odstranit uživatele")
            ->setClass("btn btn-danger btn-sm ajax");
        $grid->addAction("edit", "", "edit!")
            ->setIcon("edit")
            ->setTitle("Editovat uživatele")
            ->setClass("btn btn-primary btn-sm");
        return $grid;
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function handleDelete(int $id)
    {
        $this->userFunctionality->delete($id);
        $this["userGrid"]->reload();
        $this->informUser('Uživatel úspěšně odstraněn.', true, 'success');
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleEdit(int $id)
    {
        $this->redirect("edit", $id);
    }

    /**
     * @return UserFormControl
     */
    public function createComponentUserCreateForm(): UserFormControl
    {
        $control = $this->userFormFactory->create();
        $control->onSuccess[] = function (){
            $this["userGrid"]->reload();
            $this->informUser('Uživatel úspěšně vytvořen.', true);
        };
        $control->onError[] = function ($e){
            $this->informUser('Chyba při vytváření uživatele.', true, 'danger');
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
            $this["userGrid"]->reload();
            $this->informUser('Uživatel úspěšně editován.');
            $this->redirect('default');
        };
        $control->onError[] = function ($e){
            $this->informUser('Chyba při editaci uživatele.', false, 'danger');
            $this->redirect('default');
        };
        return $control;
    }
}