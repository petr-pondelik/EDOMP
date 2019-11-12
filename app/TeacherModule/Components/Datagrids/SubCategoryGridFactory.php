<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 10.4.19
 * Time: 19:24
 */

namespace App\TeacherModule\Components\DataGrids;

use App\CoreModule\Components\DataGrids\BaseGrid;
use App\CoreModule\Model\Persistent\Repository\CategoryRepository;
use App\CoreModule\Model\Persistent\Repository\SubCategoryRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class SubCategoryGridFactory
 * @package App\TeacherModule\Components\DataGrids
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
     * @throws \Exception
     */
    public function create($container, $name): DataGrid
    {
        $grid = parent::create($container, $name);

        $categoryOptions = $this->categoryRepository->findAssoc([], 'id');

        $grid->setPrimaryKey('id');

        $grid->setDataSource($this->subCategoryRepository->createQueryBuilder('er'));

        $grid->addColumnNumber('id', 'ID')
            ->setFitContent()
            ->setSortable()
            ->setFilterText();

        $grid->addColumnDateTime('created', 'Vytvořeno')
            ->addAttributes(['class' => 'text-center'])
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('label', 'Název')
            ->setFilterText();

        $grid->addColumnStatus('category', 'Kategorie', 'category.id')
            ->addAttributes(['class' => 'text-center'])
            ->setOptions($categoryOptions)
            ->setSortable('er.category')
            ->onChange[] = [$container, 'handleCategoryUpdate'];

        $grid->addFilterMultiSelect('category', '', $categoryOptions);

        return $grid;
    }
}