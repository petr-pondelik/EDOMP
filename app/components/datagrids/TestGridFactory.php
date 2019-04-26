<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 19.3.19
 * Time: 21:57
 */

namespace App\Components\DataGrids;

use App\Model\Managers\GroupManager;
use App\Model\Managers\SpecializationManager;
use App\Model\Managers\TestManager;

/**
 * Class TestGridFactory
 * @package app\components\datagrids
 */
class TestGridFactory extends BaseGrid
{
    /**
     * @var TestManager
     */
    protected $testManager;

    /**
     * @var GroupManager
     */
    protected $groupManager;

    /**
     * TestGridFactory constructor.
     * @param TestManager $testManager
     * @param GroupManager $groupManager
     */
    public function __construct
    (
        TestManager $testManager, GroupManager $groupManager
    )
    {
        parent::__construct();
        $this->testManager = $testManager;
        $this->groupManager = $groupManager;
    }

    /**
     * @param $container
     * @param $name
     * @return \Ublaboo\DataGrid\DataGrid
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function create($container, $name)
    {
        $grid = parent::create($container, $name);

        $groupOptions = $this->groupManager->getAll('ASC');

        $grid->setPrimaryKey('test_id');

        $grid->setDataSource($this->testManager->getSelect());

        $grid->addColumnNumber('test_id', 'ID')
            ->addAttributes(['class' => 'text-center'])
            ->setSortable();

        $grid->addColumnDateTime('created', 'Vytvořeno')
            ->addAttributes(['class' => 'text-center'])
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('group_id', 'Skupina')
            ->addAttributes(['class' => 'text-center'])
            ->setSortable()
            ->setReplacement($groupOptions)
            ->setFilterMultiSelect($groupOptions);

        return $grid;
    }
}