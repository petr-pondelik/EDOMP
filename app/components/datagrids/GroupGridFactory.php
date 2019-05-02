<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 12.4.19
 * Time: 11:39
 */

namespace App\Components\DataGrids;

use App\Helpers\ConstHelper;
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
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * GroupGridFactory constructor.
     * @param GroupRepository $groupRepository
     * @param SuperGroupRepository $superGroupRepository
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        GroupRepository $groupRepository, SuperGroupRepository $superGroupRepository, ConstHelper $constHelper
    )
    {
        parent::__construct();
        $this->groupRepository = $groupRepository;
        $this->superGroupRepository = $superGroupRepository;
        $this->constHelper = $constHelper;
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

        $superGroupOptions = $this->superGroupRepository->findAssoc([], "id");

        $grid->setPrimaryKey('id');

        $grid->setDataSource(
            $this->groupRepository->createQueryBuilder("er")
                ->where("er.id != :id")
                ->setParameter("id", $this->constHelper::ADMIN_GROUP)
        );

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