<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 10:19
 */

namespace App\TeacherModule\Components\DataGrids;

use App\CoreModule\Components\DataGrids\BaseGrid;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SecuredRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class TemplateGridFactory
 * @package App\TeacherModule\Components\DataGrids
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
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubThemeRepository $subThemeRepository
     * @param ConstHelper $constHelper
     */
    public function __construct(
        DifficultyRepository $difficultyRepository,
        ProblemTemplateRepository $problemTemplateRepository,
        ProblemTypeRepository $problemTypeRepository,
        SubThemeRepository $subThemeRepository,
        ConstHelper $constHelper
    )
    {
        parent::__construct();
        $this->difficultyRepository = $difficultyRepository;
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->subThemeRepository = $subThemeRepository;
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
    public function create($container, $name, SecuredRepository $repository = null, int $problemType = 0): DataGrid
    {
        $grid = parent::create($container, $name);

        $difficultyOptions = $this->difficultyRepository->findAssoc([], 'id');
        $subThemeOptions = $this->subThemeRepository->findAllowed($container->user);

        $grid->setPrimaryKey('id');

        $grid->setDataSource($repository->getSecuredQueryBuilder($container->user));

        $grid->addColumnNumber('id', 'ID')
            ->setFitContent()
            ->setSortable()
            ->setFilterText();

        $grid->addColumnDateTime('created', 'Vytvo??eno')
            ->addAttributes(['class' => 'text-center'])
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('textBefore', '??vod zad??n??')
            ->setFilterText();

        $grid->addColumnText('body', '??loha');

        $grid->addColumnText('textAfter', 'Dodatek k zad??n??')
            ->setFilterText();

        $grid->addColumnText('successRate', 'Pr??m. ??sp????nost')
            ->setSortable();

        $grid->addColumnStatus('studentVisible', 'Zobrazit ve cvi??ebnici', 'studentVisible')
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

        $grid->addColumnStatus('subTheme', 'T??ma', 'subTheme.id')
            ->setSortable('er.id')
            ->addAttributes(['class' => 'text-center'])
            ->setOptions($subThemeOptions)
            ->onChange[] = [$container, 'handleSubThemeUpdate'];


        $grid->addFilterMultiSelect('subTheme', '', $subThemeOptions);

        $grid->addColumnStatus('difficulty', 'Obt????nost', 'difficulty.id')
            ->setSortable('er.id')
            ->addAttributes(['class' => 'text-center'])
            ->setOptions($difficultyOptions)
            ->onChange[] = [$container, 'handleDifficultyUpdate'];

        $grid->addFilterMultiSelect('difficulty', '', $difficultyOptions);

        return $grid;
    }
}