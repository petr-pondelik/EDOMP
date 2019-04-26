<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.4.19
 * Time: 0:11
 */

namespace App\Components\DataGrids;

use App\Model\Managers\CategoryManager;

/**
 * Class CategoryGridFactory
 * @package App\Components\DataGrids
 */
class CategoryGridFactory extends BaseGrid
{

    /**
     * @var CategoryManager
     */
    protected $categoryManager;

    public function __construct
    (
        CategoryManager $categoryManager
    )
    {
        parent::__construct();
        $this->categoryManager = $categoryManager;
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

        $grid->setPrimaryKey('category_id');

        $grid->setDataSource($this->categoryManager->getSelect());

        $grid->addColumnNumber('category_id', 'ID')
            ->setSortable();

        $grid->addColumnDateTime('created', 'Vytvořeno')
            ->addAttributes(['class' => 'text-center'] )
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('label', 'Název');

        return $grid;
    }
}