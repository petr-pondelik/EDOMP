<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 12.4.19
 * Time: 11:39
 */

namespace App\Components\DataGrids;

use App\Model\Repository\GroupRepository;
use App\Model\Repository\SuperGroupRepository;

/**
 * Class GroupGridFactory
 * @package App\Components\DataGrids
 */
class GroupGridFactory extends BaseGrid
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
     * GroupGridFactory constructor.
     * @param GroupRepository $groupRepository
     * @param SuperGroupRepository $superGroupRepository
     */
    public function __construct
    (
        GroupRepository $groupRepository, SuperGroupRepository $superGroupRepository
    )
    {
        parent::__construct();
        $this->groupRepository = $groupRepository;
        $this->superGroupRepository = $superGroupRepository;
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

        $superGroupOptions = $this->superGroupRepository->findAll();

        $grid->setPrimaryKey('id');

        $grid->setDataSource($this->groupRepository->createQueryBuilder("er"));

        $grid->addColumnNumber('id', 'ID')
            ->setSortable();

        $grid->addColumnDateTime('created', 'Vytvořeno')
            ->addAttributes(['class' => 'text-center'] )
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('label', 'Název');

        if(!$isPermissions){
            $grid->addColumnStatus("superGroup", "Superskupina", "superGroup.id")
                ->setSortable("er.category")
                ->addAttributes(["class" => "text-center"])
                ->setOptions($superGroupOptions)
                ->onChange[] = [$container, 'handleSuperGroupUpdate'];
        }

        return $grid;
    }
}