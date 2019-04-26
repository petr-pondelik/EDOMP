<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 13.4.19
 * Time: 11:05
 */

namespace App\Components\DataGrids;

use App\Model\Managers\LogoManager;

/**
 * Class LogoGridFactory
 * @package App\Components\DataGrids
 */
class LogoGridFactory extends BaseGrid
{
    /**
     * @var LogoManager
     */
    protected $logoManager;

    /**
     * LogoGridFactory constructor.
     * @param LogoManager $logoManager
     */
    public function __construct
    (
        LogoManager $logoManager
    )
    {
        parent::__construct();
        $this->logoManager = $logoManager;
    }

    /**
     * @param $container
     * @param $name
     * @return \Ublaboo\DataGrid\DataGrid
     * @throws \Dibi\NotSupportedException
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function create($container, $name)
    {
        $grid = parent::create($container, $name);

        $grid->setPrimaryKey("logo_id");

        $grid->setDataSource($this->logoManager->getDatagridSelect());

        $grid->addColumnNumber("logo_id", "ID")
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