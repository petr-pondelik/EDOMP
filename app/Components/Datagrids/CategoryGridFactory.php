<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.4.19
 * Time: 0:11
 */

namespace App\Components\DataGrids;

use App\Model\Persistent\Repository\CategoryRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class CategoryGridFactory
 * @package App\Components\DataGrids
 */
class CategoryGridFactory extends BaseGrid
{

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * CategoryGridFactory constructor.
     * @param CategoryRepository $categoryRepository
     */
    public function __construct
    (
         CategoryRepository $categoryRepository
    )
    {
        parent::__construct();
        $this->categoryRepository = $categoryRepository;
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

        $grid->setDataSource($this->categoryRepository->createQueryBuilder('er'));

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