<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 11.4.19
 * Time: 18:43
 */

namespace App\Components\DataGrids;

use App\Helpers\ConstHelper;
use App\Model\Repository\SuperGroupRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class SuperGroupGridFactory
 * @package App\Components\DataGrids
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
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function create($container, $name): DataGrid
    {
        $grid = parent::create($container, $name);

        $grid->setPrimaryKey("id");

        $grid->setDataSource(
            $this->superGroupRepository->createQueryBuilder("er")
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

        return $grid;
    }
}