<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.2.19
 * Time: 13:06
 */

namespace App\Components\DataGrids;

use App\CoreModule\Components\DataGrids\BaseGrid;
use App\CoreModule\Helpers\ConstHelper;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemFinal\ProblemFinalRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class ProblemGridFactory
 * @package app\components\datagrids
 */
class ProblemGridFactory extends BaseGrid
{
    /**
     * @var DifficultyRepository
     */
    private $difficultyRepository;

    /**
     * @var ProblemFinalRepository
     */
    protected $problemRepository;

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
     * @param ProblemFinalRepository $problemRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ConstHelper $constHelper
     */
    public function __construct(
        DifficultyRepository $difficultyRepository,
        ProblemFinalRepository $problemRepository, ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository,
        ConstHelper $constHelper
    )
    {
        parent::__construct();
        $this->difficultyRepository = $difficultyRepository;
        $this->problemRepository = $problemRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->constHelper = $constHelper;
    }

    /**
     * @param $container
     * @param $name
     * @return \Ublaboo\DataGrid\DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     * @throws \Exception
     */
    public function create($container, $name): DataGrid
    {
        $grid = parent::create($container, $name);

        $difficultyOptions = $this->difficultyRepository->findAssoc([], 'id');
        $subCategoryOptions = $this->subCategoryRepository->findAssoc([], 'id');

        $grid->setPrimaryKey('id');

        $grid->setDataSource($this->problemRepository->createQueryBuilder('er'));

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

        $grid->addColumnText('result', 'Výsledek');

        $grid->addColumnText('success_rate', 'Prům. úspěšnost');

        $grid->addColumnNumber('isGenerated', 'Vygenerovaný')
            ->addAttributes(['class' => 'text-center'])
            ->setTemplateEscaping(false)
            ->setReplacement([
                0 => "<i class='fa fa-times text-danger'></i>",
                1 => "<i class='fa fa-check text-success'></i>"
            ]);

        $grid->addFilterMultiSelect('isGenerated', '', [
            0 => 'Ne',
            1 => 'Ano'
        ]);

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