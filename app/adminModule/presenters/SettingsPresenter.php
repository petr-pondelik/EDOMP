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
use App\Model\Entities\Group;
use App\Model\Managers\GroupManager;
use App\Model\Managers\SuperGroupManager;
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
     * @var GroupManager
     */
    protected $groupManager;

    /**
     * @var SuperGroupManager
     */
    protected $superGroupManager;

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
     * @param GroupManager $groupManager
     * @param SuperGroupManager $superGroupManager
     * @param GroupGridFactory $groupGridFactory
     * @param SuperGroupGridFactory $superGroupGridFactory
     * @param PermissionFormFactory $permissionFormFactory
     */
    public function __construct
    (
        GroupManager $groupManager, SuperGroupManager $superGroupManager,
        GroupGridFactory $groupGridFactory, SuperGroupGridFactory $superGroupGridFactory, PermissionFormFactory $permissionFormFactory
    )
    {
        parent::__construct();
        $this->groupManager = $groupManager;
        $this->superGroupManager = $superGroupManager;
        $this->groupGridFactory = $groupGridFactory;
        $this->superGroupGridFactory = $superGroupGridFactory;
        $this->permissionFormFactory = $permissionFormFactory;
    }

    /**
     * @param int $group_id
     * @throws \Dibi\Exception
     */
    public function actionGroupPermissionEdit(int $group_id)
    {
        $form = $this["groupPermissionForm"];
        if(!$form->isSubmitted()){
            $this->template->groupId = $group_id;
            $categories = $this->groupManager->getCategoriesIds($group_id);
            $this->setDefaults($form, $group_id, $categories);
        }
    }

    /**
     * @param int $super_group_id
     * @throws \Dibi\Exception
     */
    public function actionSuperGroupPermissionEdit(int $super_group_id)
    {
        $form = $this["superGroupPermissionForm"];
        if(!$form->isSubmitted()){
            $this->template->superGroupId = $super_group_id;
            $categories = $this->superGroupManager->getCategoriesIds($super_group_id);
            $this->setDefaults($form, $super_group_id, $categories);
        }
    }

    /**
     * @param IComponent $form
     * @param int $itemId
     * @param array $categories
     */
    public function setDefaults(IComponent $form, int $itemId, array $categories)
    {
        $form["item_id"]->setDefaultValue($itemId);
        $form["categories"]->setDefaultvalue($categories);
    }

    /**
     * @param $name
     * @return \Ublaboo\DataGrid\DataGrid
     * @throws \Dibi\NotSupportedException
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
     * @throws \Dibi\Exception
     * @throws \Nette\Application\AbortException
     */
    public function handleGroupPermissionFormSuccess(Form $form, ArrayHash $values)
    {
        $this->groupManager->updatePermissions($values->item_id, $values->categories);
        $this->flashMessage("Oprávnění skupiny úspěšně změněna.", "success");
        $this->redirect("groupPermission");
    }

    /**
     * @return Form
     */
    public function createComponentSuperGroupPermissionForm(): Form
    {
        $form = $this->permissionFormFactory->create();
        $form->onSuccess[] = [$this, "handleSuperGroupPermissionFormSuccess"];
        return $form;
    }

    public function handleSuperGroupPermissionFormSuccess(Form $form, ArrayHash $values)
    {
        $this->superGroupManager->updatePermissions($values->item_id, $values->categories);
        $this->flashMessage("Oprávnění superskupiny úspěšně změněna.", "success");
        $this->redirect("superGroupPermission");
    }
}