<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 10.4.19
 * Time: 19:24
 */

namespace App\Components\DataGrids;

use App\Model\Repository\CategoryRepository;
use App\Model\Repository\SubCategoryRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class SubCategoryGridFactory
 * @package App\Components\DataGrids
 */
class SubCategoryGridFactory extends BaseGrid
{
    /**
     * @var SubCategoryRepository
     */
    protected $subCategoryRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * SubCategoryGridFactory constructor.
     * @param SubCategoryRepository $subCategoryRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct
    (
        SubCategoryRepository $subCategoryRepository, CategoryRepository $categoryRepository
    )
    {
        parent::__construct();
        $this->subCategoryRepository = $subCategoryRepository;
        $this->categoryRepository = $categoryRepository;
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

        $categoryOptions = $this->categoryRepository->findPairs([], "label");

        bdump($categoryOptions);

        $grid->setPrimaryKey("id");

        $grid->setDataSource($this->subCategoryRepository->createQueryBuilder("er"));

        $grid->addColumnNumber("id", "ID")
            ->setSortable();

        $grid->addColumnDateTime("created", "Vytvořeno")
            ->addAttributes(["class" => "text-center"])
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('label', 'Název');

        $grid->addColumnStatus("Category.id", "Kategorie")
            ->addAttributes(["class" => "text-center"])
            ->setOptions($categoryOptions)
            ->setSortable("er.id")
            ->onChange[] = [$container, "handleCategoryUpdate"];

        return $grid;
    }
}