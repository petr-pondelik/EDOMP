<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 10:19
 */

namespace App\Components\DataGrids;

use App\CoreModule\Components\DataGrids\BaseGrid;
use App\CoreModule\Helpers\ConstHelper;
use App\Model\Persistent\Repository\BaseRepository;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class TemplateGridFactory
 * @package App\Components\DataGrids
 */
class TemplateGridFactory extends BaseGrid
{
    /**
     * @var ProblemTemplateRepository
     */
    protected $problemTemplateRepository;

    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    /**
     * @var ProblemTypeRepository
     */
    protected $problemTypeRepository;

    /**
     * @var SubCategoryRepository
     */
    protected $subCategoryRepository;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * ProblemGridFactory constructor.
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ConstHelper $constHelper
     */
    public function __construct(
        DifficultyRepository $difficultyRepository,
        ProblemTemplateRepository $problemTemplateRepository, ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository,
        ConstHelper $constHelper
    )
    {
        parent::__construct();
        $this->difficultyRepository = $difficultyRepository;
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->constHelper = $constHelper;
    }

    /**
     * @param $container
     * @param $name
     * @param $repository
     * @param int $problemType
     * @return \Ublaboo\DataGrid\DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     * @throws \Exception
     */
    public function create($container, $name, BaseRepository $repository = null, int $problemType = 0): DataGrid
    {
        $grid = parent::create($container, $name);

        $difficultyOptions = $this->difficultyRepository->findAssoc([], 'id');
        $subCategoryOptions = $this->subCategoryRepository->findAssoc([], 'id');

        $grid->setPrimaryKey('id');

        $grid->setDataSource($repository->createQueryBuilder('er'));

        $grid->addColumnNumber('id', 'ID')
            ->setFitContent()
            ->setSortable()
            ->setFilterText();

        $grid->addColumnDateTime('created', 'Vytvořeno')
            ->addAttributes(['class' => 'text-center'])
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('textBefore', 'Úvod zadání')
            ->setFilterText();

        $grid->addColumnText('body', 'Úloha');

        $grid->addColumnText('textAfter', 'Dodatek k zadání')
            ->setFilterText();

        $grid->addColumnText('success_rate', 'Prům. úspěšnost');

        $grid->addColumnStatus('subCategory', 'Téma', 'subCategory.id')
            ->setSortable('er.id')
            ->addAttributes(['class' => 'text-center'])
            ->setOptions($subCategoryOptions)
            ->onChange[] = [$container, 'handleSubCategoryUpdate'];

        $grid->addFilterMultiSelect('subCategory', '', $subCategoryOptions);

        $grid->addColumnStatus('difficulty', 'Obtížnost', 'difficulty.id')
            ->setSortable('er.id')
            ->addAttributes(['class' => 'text-center'])
            ->setOptions($difficultyOptions)
            ->onChange[] = [$container, 'handleDifficultyUpdate'];

        $grid->addFilterMultiSelect('difficulty', '', $difficultyOptions);

        return $grid;
    }
}