<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 10.4.19
 * Time: 19:24
 */

namespace App\Components\DataGrids;

use App\Model\Managers\CategoryManager;
use App\Model\Managers\SubCategoryManager;

/**
 * Class SubCategoryGridFactory
 * @package App\Components\DataGrids
 */
class SubCategoryGridFactory extends BaseGrid
{
    /**
     * @var SubCategoryManager
     */
    protected $subCategoryManager;

    /**
     * @var CategoryManager
     */
    protected $categoryManager;

    /**
     * SubCategoryGridFactory constructor.
     * @param SubCategoryManager $subCategoryManager
     * @param CategoryManager $categoryManager
     */
    public function __construct
    (
        SubCategoryManager $subCategoryManager, CategoryManager $categoryManager
    )
    {
        parent::__construct();
        $this->subCategoryManager = $subCategoryManager;
        $this->categoryManager = $categoryManager;
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

        $categoryOptions = $this->categoryManager->getAll('ASC');
        bdump($categoryOptions);

        $grid->setPrimaryKey("sub_category_id");

        $grid->setDataSource($this->subCategoryManager->getSelect());

        $grid->addColumnNumber("sub_category_id", "ID")
            ->setSortable();

        $grid->addColumnDateTime("created", "Vytvořeno")
            ->addAttributes(["class" => "text-center"])
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('label', 'Název');

        $grid->addColumnStatus("category_id", "Kategorie")
            ->setSortable()
            ->addAttributes(["class" => "text-center"])
            ->setOptions($categoryOptions)
            ->onChange[] = [$container, "handleCategoryUpdate"];

        return $grid;
    }
}