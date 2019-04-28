<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 12.4.19
 * Time: 11:39
 */

namespace App\Components\DataGrids;

use App\Model\Managers\GroupManager;
use App\Model\Managers\SuperGroupManager;

/**
 * Class GroupGridFactory
 * @package App\Components\DataGrids
 */
class GroupGridFactory extends BaseGrid
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
     * GroupGridFactory constructor.
     * @param GroupManager $groupManager
     * @param SuperGroupManager $superGroupManager
     */
    public function __construct
    (
        GroupManager $groupManager, SuperGroupManager $superGroupManager
    )
    {
        parent::__construct();
        $this->groupManager = $groupManager;
        $this->superGroupManager = $superGroupManager;
    }

    /**
     * @param $container
     * @param $name
     * @param bool $isPermissions
     * @return \Ublaboo\DataGrid\DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function create($container, $name, bool $isPermissions = false)
    {
        $grid = parent::create($container, $name);

        $superGroupOptions = $this->superGroupManager->getAllPairs("ASC");

        $grid->setPrimaryKey('group_id');

        $grid->setDataSource($this->groupManager->getSelect());

        $grid->addColumnNumber('group_id', 'ID')
            ->setSortable();

        $grid->addColumnDateTime('created', 'Vytvořeno')
            ->addAttributes(['class' => 'text-center'] )
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('label', 'Název');

        if(!$isPermissions){
            $grid->addColumnStatus("super_group_id", "Super-skupina")
                ->setSortable()
                ->addAttributes(["class" => "text-center"])
                ->setOptions($superGroupOptions)
                ->onChange[] = [$container, 'handleSuperGroupUpdate'];
        }

        return $grid;
    }
}