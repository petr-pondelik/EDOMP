<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 13.4.19
 * Time: 11:05
 */

namespace App\Components\DataGrids;

use App\Model\Repository\LogoRepository;

/**
 * Class LogoGridFactory
 * @package App\Components\DataGrids
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
     */
    public function create($container, $name)
    {
        $grid = parent::create($container, $name);

        $grid->setPrimaryKey("id");

        $grid->setDataSource($this->logoRepository->createQueryBuilder("er"));

        $grid->addColumnNumber("id", "ID")
            ->setSortable();

        $grid->addColumnDateTime('created', 'Vytvořeno')
            ->addAttributes(['class' => 'text-center'] )
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText("path", "Cesta");

        $grid->addColumnText("extension", "Formát");

        $grid->addColumnText("label", "Název");

        return $grid;
    }
}