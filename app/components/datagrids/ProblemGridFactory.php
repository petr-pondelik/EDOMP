<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.2.19
 * Time: 13:06
 */

namespace App\Components\DataGrids;

use App\Model\Managers\DifficultyManager;
use App\Model\Managers\ProblemFinalManager;
use App\Model\Managers\ProblemPrototypeManager;
use App\Model\Managers\ProblemTypeManager;
use App\Model\Managers\SubCategoryManager;
use Tracy\Debugger;

/**
 * Class ProblemGridFactory
 * @package app\components\datagrids
 */
class ProblemGridFactory extends BaseGrid
{
    /**
     * @var DifficultyManager
     */
    private $difficultyManager;

    /**
     * @var ProblemPrototypeManager
     */
    protected $problemPrototypeManager;

    /**
     * @var ProblemFinalManager
     */
    protected $problemFinalManager;

    /**
     * @var ProblemTypeManager
     */
    private $problemTypeManager;

    /**
     * @var SubCategoryManager
     */
    private $subCategoryManager;

    /**
     * ProblemGridFactory constructor.
     * @param DifficultyManager $difficultyManager
     * @param ProblemPrototypeManager $problemPrototypeManager
     * @param ProblemFinalManager $problemFinalManager
     * @param ProblemTypeManager $problemTypeManager
     * @param SubCategoryManager $subCategoryManager
     */
    public function __construct(
        DifficultyManager $difficultyManager, ProblemPrototypeManager $problemPrototypeManager,
        ProblemFinalManager $problemFinalManager, ProblemTypeManager $problemTypeManager,
        SubCategoryManager $subCategoryManager
    )
    {
        parent::__construct();
        $this->difficultyManager = $difficultyManager;
        $this->problemPrototypeManager = $problemPrototypeManager;
        $this->problemFinalManager = $problemFinalManager;
        $this->problemTypeManager = $problemTypeManager;
        $this->subCategoryManager = $subCategoryManager;
    }

    /**
     * @param $container
     * @param $name
     * @param bool $prototype
     * @return \Ublaboo\DataGrid\DataGrid
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function create($container, $name, $prototype = false)
    {
        $grid = parent::create($container, $name);

        $difficultyOptions = $this->difficultyManager->getAll('ASC');
        $typeOptions = $this->problemTypeManager->getAll('ASC');
        $subCategoryOptions = $this->subCategoryManager->getAll("ASC");

        $grid->setPrimaryKey('problem_id');

        $prototype ? $grid->setDataSource($this->problemPrototypeManager->getSelect()) : $grid->setDataSource($this->problemFinalManager->getSelect('result'));

        $grid->addColumnNumber('problem_id', 'ID')
            ->setSortable();

        $grid->addColumnDateTime('created', 'Vytvořeno')
            ->addAttributes(['class' => 'text-center'])
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('text_before', 'Zadání před');

        $grid->addColumnText('structure', 'Struktura');

        $grid->addColumnText('text_after', 'Zadání po');

        if(!$prototype)
            $grid->addColumnText('result', 'Výsledek');

        $grid->addColumnText("success_rate", "Prům. úspěšnost");

        if(!$prototype)
            $grid->addColumnNumber('is_generatable', 'Generovatelný')
                ->addAttributes(['class' => 'text-center'])
                ->setTemplateEscaping(false)
                ->setReplacement([
                    0 => "<i class='fa fa-times text-danger'></i>",
                    1 => "<i class='fa fa-check text-success'></i>"
                ]);

        $grid->addColumnNumber('problem_type_id', 'Typ')
            ->setSortable()
            ->addAttributes(['class' => 'text-center'])
            ->setReplacement($typeOptions)
            ->setFilterMultiSelect($typeOptions);

        $grid->addColumnStatus("sub_category_id", "Téma")
            ->setSortable()
            ->addAttributes(["class" => "text-center"])
            ->setOptions($subCategoryOptions)
            ->onChange[] = [$container, "handleSubCategoryUpdate"];

        $grid->addFilterMultiSelect("sub_category_id", "", $subCategoryOptions);

        $grid->addColumnStatus('difficulty_id', 'Obtížnost')
            ->setSortable()
            ->addAttributes(['class' => 'text-center'])
            ->setOptions($difficultyOptions)
            ->onChange[] = [$container, 'handleDifficultyUpdate'];
        $grid->addFilterMultiSelect('difficulty_id', '', $difficultyOptions);

        return $grid;
    }
}