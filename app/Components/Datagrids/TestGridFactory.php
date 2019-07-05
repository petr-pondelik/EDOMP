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

        $grid->setPrimaryKey('id');

        $grid->setDataSource($this->testRepository->createQueryBuilder('er'));

        $grid->addColumnNumber('id', 'ID')
            ->setFitContent()
            ->setSortable()
            ->setFilterText();

        $grid->addColumnDateTime('created', 'Vytvořeno')
            ->addAttributes(['class' => 'text-center'])
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('schoolYear', 'Školní rok')
            ->setFilterText();

        $grid->addColumnText('term', 'Období')
            ->setFilterText();

        $grid->addColumnNumber('testNumber', 'Číslo testu')
            ->setFilterText();

        return $grid;
    }
}