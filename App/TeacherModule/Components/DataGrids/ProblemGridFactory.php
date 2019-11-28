<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.2.19
 * Time: 13:06
 */

namespace App\TeacherModule\Components\DataGrids;

use App\CoreModule\Components\DataGrids\BaseGrid;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemFinalRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class ProblemGridFactory
 * @package App\TeacherModule\Components\DataGrids
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
     * @var SubThemeRepository
     */
    protected $subThemeRepository;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * ProblemGridFactory constructor.
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemFinalRepository $problemRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubThemeRepository $subThemeRepository
     * @param ConstHelper $constHelper
     */
    public function __construct(
        DifficultyRepository $difficultyRepository,
        ProblemFinalRepository $problemRepository,
        ProblemTypeRepository $problemTypeRepository,
        SubThemeRepository $subThemeRepository,
        ConstHelper $constHelper
    )
    {
        parent::__construct();
        $this->difficultyRepository = $difficultyRepository;
        $this->problemRepository = $problemRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->subThemeRepository = $subThemeRepository;
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
        $subThemeOptions = $this->subThemeRepository->findAllowed($container->user);

        $grid->setPrimaryKey('id');

        $grid->setDataSource($this->problemRepository->getSecuredQueryBuilder($container->user));

        bdump($this->problemRepository->findAll());

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

        $grid->addColumnStatus('studentVisible', 'Zobrazit ve cvičebnici', 'studentVisible')
            ->addAttributes(['class' => 'text-center'])
            ->setOptions([
                0 => 'Ne',
                1 => 'Ano'
            ])
            ->onChange[] = [$container, 'handleStudentVisibleUpdate'];

        $grid->addFilterMultiSelect('studentVisible', '', [
            0 => 'Ne',
            1 => 'Ano'
        ]);

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

        $grid->addColumnStatus('subTheme', 'Téma', 'subTheme.id')
            ->setSortable('er.id')
            ->addAttributes(['class' => 'text-center'])
            ->setOptions($subThemeOptions)
            ->onChange[] = [$container, 'handleSubThemeUpdate'];

        $grid->addFilterMultiSelect('subTheme', '', $subThemeOptions);

        $grid->addColumnStatus('difficulty', 'Obtížnost', 'difficulty.id')
            ->setSortable('er.id')
            ->addAttributes(['class' => 'text-center'])
            ->setOptions($difficultyOptions)
            ->onChange[] = [$container, 'handleDifficultyUpdate'];

        $grid->addFilterMultiSelect('difficulty', '', $difficultyOptions);

        return $grid;
    }
}