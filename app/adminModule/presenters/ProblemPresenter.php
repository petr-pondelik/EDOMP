<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 17:18
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\ProblemGridFactory;
use App\Components\Forms\ProblemFormFactory;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\ProblemTypeRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class ProblemPresenter
 * @package App\AdminModule\Presenters
 */
class ProblemPresenter extends AdminPresenter
{

    /**
     * @var ProblemGridFactory
     */
    protected $problemGridFactory;

    /**
     * @var ProblemFormFactory
     */
    protected $problemFormFactory;

    /**
     * @var ProblemRepository
     */
    protected $problemRepository;

    /**
     * @var ProblemTypeRepository
     */
    protected $problemTypeRepository;

    /**
     * @var ProblemConditionRepository
     */
    protected $problemConditionRepository;

    /**
     * ProblemPresenter constructor.
     * @param ProblemGridFactory $problemGridFactory
     * @param ProblemFormFactory $problemFormFactory
     * @param ProblemRepository $problemRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     */
    public function __construct
    (
        ProblemGridFactory $problemGridFactory, ProblemFormFactory $problemFormFactory,
        ProblemRepository $problemRepository, ProblemTypeRepository $problemTypeRepository,
        ProblemConditionRepository $problemConditionRepository
    )
    {
        parent::__construct();
        $this->problemGridFactory = $problemGridFactory;
        $this->problemFormFactory = $problemFormFactory;
        $this->problemRepository = $problemRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
    }

    public function renderDefault()
    {
        $types = $this->problemTypeRepository->findAssoc([], "id");
        $this->template->problemTypes = $types;
        $this->template->condByProblemTypes = [];
        foreach ($types as $key => $type){
            $this->template->condByProblemTypes[$key] = $type->getConditionTypes()->getValues();
        }
    }

    /**
     * @param $name
     * @return DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentProblemGrid($name): DataGrid
    {
        $grid = $this->problemGridFactory->create($this, $name);

        /*$grid->addAction('delete', '', 'delete!')
            ->setTemplate(__DIR__ . '/templates/ProblemFinal/removeBtn.latte');

        $grid->addAction('getResult', 'Získat výsledek')
            ->setTitle('Získat výsledek')
            ->setTemplate(__DIR__ . '/templates/ProblemFinal/getResultColumn.latte');

        $grid->addAction('edit', 'Edit')
            ->setTitle('Editovat')
            ->setTemplate(__DIR__ . '/templates/ProblemFinal/editColumn.latte');

        $grid->addInlineEdit('problem.problem_id')
            ->setIcon('pencil-alt')
            ->setTitle('Upravit inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = function($container) {
            $container->addText('text_before', '');
            $container->addText('structure', '');
            $container->addText('text_after', '');
            $container->addText('result', '');
        };

        $grid->getInlineEdit()->onSetDefaults[] = function($cont, $item) {
            bdump($item);
            $cont->setDefaults([
                'text_before' => $item->text_before,
                'structure' => $item->structure,
                'text_after' => $item->text_after,
                'result' => $item->result
            ]);
        };

        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleUpdate'];*/

        return $grid;
    }

    public function createComponentProblemCreateForm()
    {
        $form = $this->problemFormFactory->create();
        $form->addTextArea('result', 'Výsledek')
            ->setHtmlAttribute('class', 'form-control');
        $form->onValidate[] = [$this, 'handleFormValidate'];
        $form->onSuccess[] = [$this, 'handleCreateFormSuccess'];
        return $form;
    }

}