<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 11.4.19
 * Time: 18:43
 */

namespace App\TeacherModule\Components\DataGrids;

use App\CoreModule\Components\DataGrids\BaseGrid;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Model\Persistent\Repository\SuperGroupRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class SuperGroupGridFactory
 * @package App\TeacherModule\Components\DataGrids
 */
class SuperGroupGridFactory extends BaseGrid
{
    /**
     * @var SuperGroupRepository
     */
    protected $superGroupRepository;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * SuperGroupGridFactory constructor.
     * @param SuperGroupRepository $superGroupRepository
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        SuperGroupRepository $superGroupRepository, ConstHelper $constHelper
    )
    {
        parent::__construct();
        $this->superGroupRepository = $superGroupRepository;
        $this->constHelper = $constHelper;
    }

    /**
     * @param $container
     * @param $name
     * @return DataGrid
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function create($container, $name): DataGrid
    {
        $grid = parent::create($container, $name);

        $grid->setPrimaryKey('id');

        $grid->setDataSource($this->superGroupRepository->getSecuredQueryBuilder($container->user));

        $grid->addColumnNumber('id', 'ID')
            ->setFitContent()
            ->setSortable()
            ->setFilterText();

        $grid->addColumnDateTime('created', 'Vytvořeno')
            ->addAttributes(['class' => 'text-center'] )
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('label', 'Název')
            ->setFilterText();

        return $grid;
    }
}