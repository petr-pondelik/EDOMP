<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 13.4.19
 * Time: 11:05
 */

namespace App\TeacherModule\Components\DataGrids;

use App\CoreModule\Components\DataGrids\BaseGrid;
use App\CoreModule\Model\Persistent\Repository\LogoRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class LogoGridFactory
 * @package App\TeacherModule\Components\DataGrids
 */
class LogoGridFactory extends BaseGrid
{
    /**
     * @var LogoRepository
     */
    protected $logoRepository;

    /**
     * LogoGridFactory constructor.
     * @param LogoRepository $logoRepository
     */
    public function __construct
    (
        LogoRepository $logoRepository
    )
    {
        parent::__construct();
        $this->logoRepository = $logoRepository;
    }

    /**
     * @param $container
     * @param $name
     * @return \Ublaboo\DataGrid\DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function create($container, $name): DataGrid
    {
        $grid = parent::create($container, $name);

        $grid->setPrimaryKey('id');

        $grid->setDataSource($this->logoRepository->getSecuredQueryBuilder($container->user));

        $grid->addColumnNumber('id', 'ID')
            ->setFitContent()
            ->setSortable()
            ->setFilterText();

        $grid->addColumnDateTime('created', 'Vytvořeno')
            ->addAttributes(['class' => 'text-center'] )
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('path', 'Cesta')
            ->setFilterText();

        $grid->addColumnText('extension', 'Formát')
            ->setFilterText();

        $grid->addColumnText('label', 'Název')
            ->setFilterText();

        return $grid;
    }
}