<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.4.19
 * Time: 0:11
 */

namespace App\TeacherModule\Components\DataGrids;

use App\CoreModule\Components\DataGrids\BaseGrid;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class ThemeGridFactory
 * @package App\TeacherModule\Components\DataGrids
 */
class ThemeGridFactory extends BaseGrid
{

    /**
     * @var ThemeRepository
     */
    protected $themeRepository;

    /**
     * ThemeGridFactory constructor.
     * @param ThemeRepository $themeRepository
     */
    public function __construct
    (
         ThemeRepository $themeRepository
    )
    {
        parent::__construct();
        $this->themeRepository = $themeRepository;
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

        $grid->setDataSource($this->themeRepository->getSecuredQueryBuilder($container->user));

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