<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 19.3.19
 * Time: 21:57
 */

namespace App\Components\DataGrids;

use App\Model\Repository\GroupRepository;
use App\Model\Repository\TestRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class TestGridFactory
 * @package app\components\datagrids
 */
class TestGridFactory extends BaseGrid
{
    /**
     * @var TestRepository
     */
    protected $testRepository;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * TestGridFactory constructor.
     * @param TestRepository $testRepository
     * @param GroupRepository $groupRepository
     */
    public function __construct
    (
        TestRepository $testRepository, GroupRepository $groupRepository
    )
    {
        parent::__construct();
        $this->testRepository = $testRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param $container
     * @param $name
     * @return \Ublaboo\DataGrid\DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function create($container, $name): DataGrid
    {
        $grid = parent::create($container, $name);

        $groupOptions = $this->groupRepository->findAssoc([], "id");

        $grid->setPrimaryKey('id');

        $grid->setDataSource($this->testRepository->createQueryBuilder("er"));

        $grid->addColumnNumber('id', 'ID')
            ->addAttributes(['class' => 'text-center'])
            ->setSortable();

        $grid->addColumnDateTime('created', 'Vytvořeno')
            ->addAttributes(['class' => 'text-center'])
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('group', 'Skupina', "group.id")
            ->addAttributes(['class' => 'text-center'])
            ->setSortable("er.id")
            ->setReplacement($groupOptions)
            ->setFilterMultiSelect($groupOptions);

        return $grid;
    }
}