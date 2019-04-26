<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.2.19
 * Time: 21:36
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\ProblemGridFactory;
use App\Exceptions\StringFormatException;
use App\Helpers\ConstHelper;
use App\Model\Entities\Condition;
use App\Model\Entities\Problem;
use App\Model\Entities\ProblemFinal;
use App\Model\Managers\ConditionManager;
use App\Model\Managers\ConditionTypeManager;
use App\Components\Forms\ProblemFormFactory;
use App\Model\Managers\ProblemFinalManager;
use App\Model\Managers\ProblemManager;
use App\Model\Managers\ProblemPrototypeManager;
use App\Model\Managers\ProblemTypeManager;
use App\Services\MathService;
use App\Services\ValidationService;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

use Nette;

/**
 * Class ProblemFinalPresenter
 * @package App\Presenters
 */
class ProblemFinalPresenter extends ProblemPresenter
{
    /**
     * @var ProblemFinalManager
     */
    protected $problemFinalManager;

    /**
     * ProblemFinalPresenter constructor.
     * @param ProblemManager $problemManager
     * @param ProblemPrototypeManager $problemPrototypeManager
     * @param ProblemTypeManager $problemTypeManager
     * @param ConditionManager $conditionManager
     * @param ConditionTypeManager $conditionTypeManager
     * @param ProblemFinalManager $problemFinalManager
     * @param ProblemGridFactory $problemGridFactory
     * @param ProblemFormFactory $problemFormFactory
     * @param ValidationService $validationService
     * @param MathService $mathService
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        ProblemManager $problemManager, ProblemPrototypeManager $problemPrototypeManager, ProblemTypeManager $problemTypeManager,
        ConditionManager $conditionManager, ConditionTypeManager $conditionTypeManager, ProblemFinalManager $problemFinalManager,
        ProblemGridFactory $problemGridFactory, ProblemFormFactory $problemFormFactory,
        ValidationService $validationService, MathService $mathService,
        ConstHelper $constHelper
    )
    {
        parent::__construct(
            $mathService,
            $problemManager, $problemPrototypeManager, $problemTypeManager,
            $conditionManager, $conditionTypeManager,
            $problemGridFactory, $problemFormFactory, $validationService,
            $constHelper
        );
        $this->problemFinalManager = $problemFinalManager;
    }

    /**
     * @param int $problem_id
     * @throws \Dibi\Exception
     */
    public function actionEdit(int $problem_id)
    {
        $form = $this['problemEditForm'];
        if(!$form->isSubmitted()){
            $record = $this->problemFinalManager->getFinalById($problem_id, 'result');
            $conditions = $this->conditionManager->getByProblem($problem_id);
            bdump($conditions);
            $this->template->problemId = $problem_id;
            $this->setDefaults($form, $record, $conditions);
        }
    }

    /**
     * @param IComponent $form
     * @param ProblemFinal $record
     * @param Condition[] $conditions
     */
    private function setDefaults(IComponent $form, ProblemFinal $record, array $conditions)
    {
        bdump($form);
        $form['id']->setDefaultValue($record->problem_id);
        $form['idHidden']->setDefaultValue($record->problem_id);
        $form['type']->setDefaultValue($record->problem_type_id);
        $form['before']->setDefaultValue($record->text_before);
        $form['structure']->setDefaultValue($record->structure);
        $form['after']->setDefaultValue($record->text_after);
        $form['difficulty']->setDefaultValue($record->difficulty_id);
        $form['result']->setDefaultValue($record->result);
        $form["subcategory"]->setDefaultValue($record->sub_category_id);
        $form["first_n_arithmetic_seq"]->setDefaultValue($record->first_n);
        $form["first_n_geometric_seq"]->setDefaultValue($record->first_n);

        foreach($conditions as $condition)
            $form['condition_' . $condition->condition_type_id]->setValue($condition->accessor);
    }

    /**
     * @param $name
     * @throws \Dibi\Exception
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentProblemGrid($name)
    {
        $grid = $this->problemGridFactory->create($this, $name);

        $grid->addAction('delete', '', 'delete!')
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

        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleUpdate'];
    }

    /**
     * @param int $id
     * @param $row
     * @throws \Dibi\Exception
     */
    public function handleUpdate(int $id, $row)
    {
        $this->problemFinalManager->updateFinalInline($id,
            [
                'text_before' => $row->text_before,
                'structure' => $row->structure,
                'text_after' => $row->text_after
            ],
            [
                'result' => $row->result
            ]
        );
        $this->flashMessage('Příklad úspěšně editován.', 'success');
        $this->redrawControl('flashesSnippet');
    }

    /**
     * @param int $id
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function handleGetResult(int $id)
    {
        bdump($id);
        $problem = $this->problemManager->getById($id);
        bdump($problem);

        $result = null;

        try{
            $result = $this->mathService->evaluate[(int) $problem->problem_type_id]($problem);
        } catch (StringFormatException $e){
            $this->flashMessage('Při výpočtu výsledku nastala chyba.', 'danger');
        }

        bdump($result);

        $this->problemFinalManager->storeResult($id, $result);

        $this->flashMessage('Výsledek úspěšně získán.', 'success');

        $this["problemGrid"]->reload();
        $this->redrawControl('flashesSnippet');
    }

    /**
     * @param int $problem_id
     * @throws \Dibi\Exception
     */
    public function handleDelete(int $problem_id)
    {
        $this->problemFinalManager->delete($problem_id);
        $this['problemGrid']->reload();
        $this->flashMessage('Příklad úspěšně odstraněn.', 'success');
        $this->redrawControl('flashesSnippet');
    }


    /**
     * @param bool $generatableTypes
     * @return Form
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function createComponentProblemCreateForm()
    {
        $form = $this->problemFormFactory->create();
        $form->addTextArea('result', 'Výsledek')
            ->setHtmlAttribute('class', 'form-control');
        $form->onValidate[] = [$this, 'handleFormValidate'];
        $form->onSuccess[] = [$this, 'handleCreateFormSuccess'];
        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form)
    {
        $values = $form->getValues();
        bdump($values);

        //First validate problem structure
        $validateFields["structure"] = ArrayHash::from([
            "structure" => $values->structure,
            "variable" => false
        ]);
        $validationErrors = $this->validationService->validate($validateFields);
        bdump($validationErrors);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
                $this->redrawControl($veKey.'ErrorSnippet');
            }
        }

        $this->redrawControl("flashesSnippet");
        $this->redrawControl('structureErrorSnippet');
    }

    /**
     * @param Form $form
     * @param Nette\Utils\ArrayHash $values
     * @throws \Exception
     */
    public function handleCreateFormSuccess(Form $form, Nette\Utils\ArrayHash $values)
    {
        $this->problemFinalManager->createFinal($values);
        $this['problemGrid']->reload();
        $this->flashMessage('Příklad úspěšně vytvořen.', 'success');
        $this->redrawControl('content');
    }

    /**
     * @return Form
     * @throws \Dibi\Exception
     */
    public function createComponentProblemEditForm()
    {
        $form = parent::createComponentProblemEditForm();
        $form->addTextArea('result', 'Výsledek')
            ->setHtmlAttribute('class', 'form-control');
        $form->onValidate[] = [$this, 'handleFormValidate'];
        $form->onSuccess[] = [$this, "handleEditFormSuccess"];
        return $form;
    }

    /**
     * @param Form $form
     * @param Nette\Utils\ArrayHash $values
     * @throws Nette\Application\AbortException
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function handleEditFormSuccess(Form $form, Nette\Utils\ArrayHash $values)
    {
        $firstN = null;
        if($values->type === $this->constHelper::ARITHMETIC_SEQ)
            $firstN = $values->first_n_arithmetic_seq;
        else
            $firstN = $values->first_n_geometric_seq;

        $this->problemFinalManager->updateFinal($values->idHidden,
            [
                'text_before' => $values->before,
                'structure' => $values->structure,
                'text_after' => $values->after,
                'difficulty_id' => $values->difficulty,
                'problem_type_id' => $values->type,
                "sub_category_id" => $values->subcategory,
                "first_n" => $firstN
            ],
            [
                'result' => $values->result
            ],
            [
                '1' => $values->{'condition_1'},
                '2' => $values->{'condition_2'}
            ]
        );

        $this->flashMessage('Příklad úspěšně editován.', 'success');
        $this->redirect('ProblemFinal:default');
    }

}