<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 31.5.19
 * Time: 21:26
 */

namespace App\Components\DataGrids;

use App\Model\Repository\ProblemTypeRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class ProblemTypeGridFactory
 * @package App\Components\DataGrids
 */
class ProblemTypeGridFactory extends BaseGrid
{
    /**
     * @var ProblemTypeRepository
     */
    protected $problemTypeRepository;

    /**
     * ProblemTypeGridFactory constructor.
     * @param ProblemTypeRepository $problemTypeRepository
     */
    public function __construct(ProblemTypeRepository $problemTypeRepository)
    {
        parent::__construct();
        $this->problemTypeRepository = $problemTypeRepository;
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

        $grid->setPrimaryKey('id');

        $grid->setDataSource($this->problemTypeRepository->createQueryBuilder('er'));

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