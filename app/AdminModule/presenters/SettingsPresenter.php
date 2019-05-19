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
use App\Components\Forms\PermissionFormFactory;
use App\Model\Functionality\GroupFunctionality;
use App\Model\Functionality\SuperGroupFunctionality;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\SuperGroupRepository;
use App\Service\Authorizator;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Nette\Utils\ArrayHash;

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
     * @var GroupFunctionality
     */
    protected $groupFunctionality;

    /**
     * @var SuperGroupRepository
     */
    protected $superGroupRepository;

    /**
     * @var SuperGroupFunctionality
     */
    protected $superGroupFunctionality;

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
     * @param GroupRepository $groupRepository
     * @param GroupFunctionality $groupFunctionality
     * @param SuperGroupRepository $superGroupRepository
     * @param SuperGroupFunctionality $superGroupFunctionality
     * @param GroupGridFactory $groupGridFactory
     * @param SuperGroupGridFactory $superGroupGridFactory
     * @param PermissionFormFactory $permissionFormFactory
     */
    public function __construct
    (
        Authorizator $authorizator,
        GroupRepository $groupRepository, GroupFunctionality $groupFunctionality,
        SuperGroupRepository $superGroupRepository, SuperGroupFunctionality $superGroupFunctionality,
        GroupGridFactory $groupGridFactory, SuperGroupGridFactory $superGroupGridFactory, PermissionFormFactory $permissionFormFactory
    )
    {
        parent::__construct($authorizator);
        $this->groupRepository = $groupRepository;
        $this->groupFunctionality = $groupFunctionality;
        $this->superGroupRepository = $superGroupRepository;
        $this->superGroupFunctionality = $superGroupFunctionality;
        $this->groupGridFactory = $groupGridFactory;
        $this->superGroupGridFactory = $superGroupGridFactory;
        $this->permissionFormFactory = $permissionFormFactory;
    }

    /**
     * @param int $id
     */
    public function actionGroupPermissionEdit(int $id)
    {
        $form = $this["groupPermissionForm"];
        if(!$form->isSubmitted()){
            $this->template->id = $id;
            $categories = $this->groupRepository->find($id)->getCategoriesId();
            $this->setDefaults($form, $id, $categories);
        }
    }

    /**
     * @param int $id
     */
    public function actionSuperGroupPermissionEdit(int $id)
    {
        $form = $this["superGroupPermissionForm"];
        if(!$form->isSubmitted()){
            $this->template->id = $id;
            $categories = $this->superGroupRepository->find($id)->getCategoriesId();
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
     * @return Form
     * @throws \Exception
     */
    public function createComponentGroupPermissionForm(): Form
    {
        $form = $this->permissionFormFactory->create();
        $form->onSuccess[] = [$this, "handleGroupPermissionFormSuccess"];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Nette\Application\AbortException
     * @throws \Exception
     */
    public function handleGroupPermissionFormSuccess(Form $form, ArrayHash $values)
    {
        bdump($values);
        $this->groupFunctionality->updatePermissions($values->id, $values->categories);
        $this->flashMessage("Oprávnění skupiny úspěšně změněna.", "success");
        $this->redirect("groupPermission");
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentSuperGroupPermissionForm(): Form
    {
        $form = $this->permissionFormFactory->create();
        $form->onSuccess[] = [$this, "handleSuperGroupPermissionFormSuccess"];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Nette\Application\AbortException
     * @throws \Exception
     */
    public function handleSuperGroupPermissionFormSuccess(Form $form, ArrayHash $values)
    {
        $this->superGroupFunctionality->updatePermissions($values->id, $values->categories);
        $this->flashMessage("Oprávnění superskupiny úspěšně změněna.", "success");
        $this->redirect("superGroupPermission");
    }
}