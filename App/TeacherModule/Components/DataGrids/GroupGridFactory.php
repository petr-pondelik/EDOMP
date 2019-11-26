<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 12.4.19
 * Time: 11:39
 */

namespace App\TeacherModule\Components\DataGrids;

use App\CoreModule\Components\DataGrids\BaseGrid;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Model\Persistent\Repository\GroupRepository;
use App\CoreModule\Model\Persistent\Repository\SuperGroupRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class GroupGridFactory
 * @package App\TeacherModule\Components\DataGrids
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
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function create($container, $name, bool $isPermissions = false): DataGrid
    {
        $grid = parent::create($container, $name);

        $superGroupOptions = $this->superGroupRepository->findAllowed($container->user);

        $grid->setPrimaryKey('id');

        $grid->setDataSource($this->groupRepository->getSecuredQueryBuilder($container->user, true));

        $grid->addColumnNumber('id', 'ID')
            ->setFitContent()
            ->setSortable()
            ->setFilterText();

        $grid->addColumnDateTime('created', 'Vytvořeno')
            ->addAttributes(['class' => 'text-center'])
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('label', 'Název')
            ->setFilterText();

        if (!$isPermissions) {
            $grid->addColumnStatus('superGroup', 'Superskupina', 'superGroup.id')
                ->setSortable('er.theme')
                ->addAttributes(['class' => 'text-center'])
                ->setOptions($superGroupOptions)
                ->onChange[] = [$container, 'handleSuperGroupUpdate'];
        }

        $grid->addFilterMultiSelect('superGroup', '', $superGroupOptions);

        return $grid;
    }
}