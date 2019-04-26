<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.2.19
 * Time: 22:27
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\ProblemGridFactory;
use App\Components\Forms\ProblemFormFactory;
use App\Exceptions\StringFormatException;
use App\Helpers\ConstHelper;
use App\Model\Entities\Condition;
use App\Model\Entities\Problem;
use App\Model\Managers\ConditionManager;
use App\Model\Managers\ConditionTypeManager;
use App\Model\Managers\ProblemManager;
use App\Model\Managers\ProblemPrototypeManager;
use App\Model\Managers\ProblemTypeManager;
use App\Services\MathService;
use App\Services\ValidationService;
use Nette\Application\UI\Form;

use Nette;

use Nette\ComponentModel\IComponent;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemPrototypePresenter
 * @package App\Presenters
 */
class ProblemPresenter extends AdminPresenter
{
    /**
     * @var MathService
     */
    protected $mathService;

    /**
     * @var ProblemManager
     */
    protected $problemManager;

    /**
     * @var ProblemPrototypeManager
     */
    protected $problemPrototypeManager;

    /**
     * @var ProblemTypeManager
     */
    protected $problemTypeManager;

    /**
     * @var ConditionManager
     */
    protected $conditionManager;

    /**
     * @var ConditionTypeManager
     */
    protected $conditionTypeManager;

    /**
     * @var ProblemGridFactory
     */
    protected $problemGridFactory;

    /**
     * @var ProblemFormFactory
     */
    protected $problemFormFactory;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * ProblemPresenter constructor.
     * @param MathService $mathService
     * @param ProblemManager $problemManager
     * @param ProblemPrototypeManager $problemPrototypeManager
     * @param ProblemTypeManager $problemTypeManager
     * @param ConditionManager $conditionManager
     * @param ConditionTypeManager $conditionTypeManager
     * @param ProblemGridFactory $problemGridFactory
     * @param ProblemFormFactory $problemFormFactory
     * @param ValidationService $validationService
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        MathService $mathService,
        ProblemManager $problemManager, ProblemPrototypeManager $problemPrototypeManager,
        ProblemTypeManager $problemTypeManager, ConditionManager $conditionManager, ConditionTypeManager $conditionTypeManager,
        ProblemGridFactory $problemGridFactory, ProblemFormFactory $problemFormFactory,
        ValidationService $validationService,
        ConstHelper $constHelper
    )
    {
        parent::__construct();
        $this->mathService = $mathService;
        $this->problemManager = $problemManager;
        $this->problemPrototypeManager = $problemPrototypeManager;
        $this->problemTypeManager = $problemTypeManager;
        $this->conditionManager = $conditionManager;
        $this->conditionTypeManager = $conditionTypeManager;
        $this->problemGridFactory = $problemGridFactory;
        $this->problemFormFactory = $problemFormFactory;
        $this->validationService = $validationService;
        $this->constHelper = $constHelper;
    }

    public function renderDefault()
    {
        $types = $this->problemTypeManager->getAll('ASC');
        $this->template->problemTypes = $types;
        $this->template->condByProblemTypes = [];
        foreach ($types as $key => $type){
            $this->template->condByProblemTypes[$key] = $this->conditionTypeManager->getByProblemType($key);
        }
    }

    /**
     * @param int $problem_id
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function actionEdit(int $problem_id)
    {
        $form = $this['problemEditForm'];
        if(!$form->isSubmitted()){
            $record = $this->problemPrototypeManager->getById($problem_id);
            $conditions = $this->conditionManager->getByProblem($problem_id);
            $this->template->problemId = $problem_id;

            $this->setDefaults($form, $record, $conditions);
        }
    }

    /**
     * @param IComponent $form
     * @param Problem $record
     * @param Condition[] $conditions
     */
    private function setDefaults(IComponent $form, Problem $record, array $conditions)
    {
        $form['id']->setDefaultValue($record->problem_id);
        $form['idHidden']->setDefaultValue($record->problem_id);
        $form['type']->setDefaultValue($record->problem_type_id);
        $form['before']->setDefaultValue($record->text_before);
        $form['structure']->setDefaultValue($record->structure);
        $form["variable"]->setDefaultValue($record->variable);
        $form['after']->setDefaultValue($record->text_after);
        $form['difficulty']->setDefaultValue($record->difficulty_id);
        $form["subcategory"]->setDefaultValue($record->sub_category_id);
        $form["first_n_arithmetic_seq"]->setDefaultValue($record->first_n);
        $form["first_n_geometric_seq"]->setDefaultValue($record->first_n);

        foreach($conditions as $condition)
            $form['condition_' . $condition->condition_type_id]->setDefaultValue($condition->accessor);
    }

    public function renderEdit()
    {
        $types = $this->problemTypeManager->getAll('ASC');
        $this->template->problemTypes = $types;
        $this->template->condByProblemTypes = [];
        foreach ($types as $key => $type){
            $this->template->condByProblemTypes[$key] = $this->conditionTypeManager->getByProblemType($key);
        }
    }

    /**
     * @param $name
     * @throws \Dibi\Exception
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentProblemGrid($name)
    {
        $grid = $this->problemGridFactory->create($this, $name, true);

        $grid->addAction('delete', '', 'delete!')
            ->setTemplate(__DIR__ . '/templates/Problem/removeBtn.latte');

        $grid->addAction('edit', '')
            ->setTemplate(__DIR__ . '/templates/Problem/editColumn.latte');
    }

    /**
     * @param bool $generatableTypes
     * @return Form
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function createComponentProblemCreateForm()
    {
        $form = $this->problemFormFactory->create(true);
        $form->onValidate[] = [$this, 'handleFormValidate'];
        $form->onSuccess[] = [$this, 'handleCreateFormSuccess'];
        return $form;
    }

    /**
     * @param Form $form
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handleFormValidate(Form $form)
    {
        $values = $form->getValues();
        //First validate problem structure
        $validateFields["variable"] = $values->variable;
        $validateFields["structure"] = ArrayHash::from([
            "structure" => $values->structure,
            "variable" => $values->variable
        ]);

        $validationErrors = $this->validationService->validate($validateFields);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
            }
            $this->redrawFormErrors();
            return;
        }

        $standardized = "";

        if(in_array($values->type, $this->constHelper::EQUATIONS)){
            try{
                $standardized = $this->mathService->standardizeEquation($values->structure);
            } catch (StringFormatException $e){
                $form["structure"]->addError($e->getMessage());
                $this->redrawFormErrors();
                return;
            }
        }

        bdump($standardized);

        $validateFields = [];

        //Then validate if the entered problem corresponds to the selected type
        $validateFields["type"] = [
            "type_" . $values->type => ArrayHash::from([
                "structure" => $values->structure,
                "standardized" => $standardized,
                "variable" => $values->variable
            ])
        ];

        bdump($validateFields);

        $validationErrors = $this->validationService->validate($validateFields);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
            }
            $this->redrawFormErrors();
            return;
        };

        $validateFields = [];

        //Then validate if all the conditions has been satisfied
        $validateFields["conditions_valid"] = $values->conditions_valid;
        $validationErrors = $this->validationService->validate($validateFields);

        if(isset($validationErrors['conditions_valid'])){
            foreach($validationErrors['conditions_valid'] as $error){
                $form['prototype_create_submit']->addError($error);
            }
        }

        $this->redrawFormErrors();
    }

    /**
     * @param Form $form
     * @param Nette\Utils\ArrayHash $values
     * @throws \Exception
     */
    public function handleCreateFormSuccess(Form $form, Nette\Utils\ArrayHash $values)
    {
        /*
         * If there should be more than one condition type attached to the problem prototype (for example not only discriminant conditions),
         * than there should be operation of merging the matches json arrays corresponding to the prototype (so result should be array that matches all the conditions)
         * */

        $this->problemPrototypeManager->createPrototype($values);
        $this['problemGrid']->reload();
        $this->flashMessage('Šablona úspěšně vytvořena.', 'success');
        $this->redrawControl('content');
    }

    /**
     * @return Form
     * @throws \Dibi\Exception
     */
    public function createComponentProblemEditForm()
    {
        $form = $this->problemFormFactory->create();
        $form->addText('id', 'ID')
            ->setDisabled()
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('problem-id');
        $form->addHidden('idHidden');
        $form->onValidate[] = [$this, "handleFormValidate"];
        $form->onSuccess[] = [$this, "handleEditFormSuccess"];
        return $form;
    }

    /**
     * @param Form $form
     * @param Nette\Utils\ArrayHash $values
     * @throws Nette\Application\AbortException
     * @throws Nette\Utils\JsonException
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values)
    {
        bdump($values);

        $firstN = null;
        if($values->type === $this->constHelper::ARITHMETIC_SEQ)
            $firstN = $values->first_n_arithmetic_seq;
        else
            $firstN = $values->first_n_geometric_seq;

        $this->problemPrototypeManager->updatePrototype($values->idHidden,
            [
                'text_before' => $values->before,
                'structure' => $values->structure,
                "variable" => $values->variable,
                'text_after' => $values->after,
                'difficulty_id' => $values->difficulty,
                'problem_type_id' => $values->type,
                "sub_category_id" => $values->subcategory,
                "first_n" => $firstN
            ],
            [
                '1' => $values->{'condition_' . 1},
                '2' => $values->{'condition_' . 2}
            ]
        );

        $this->flashMessage('Šablona úspěšně editována.', 'success');
        $this->redirect("default");
    }

    /**
     * @param int $problemId
     * @param $row
     * @throws \Dibi\Exception
     */
    public function handleUpdate(int $problemId, $row)
    {
        $this->problemPrototypeManager->update($problemId,
            [
                'text_before' => $row->text_before,
                'structure' => $row->structure,
                'text_after' => $row->text_after
            ]
        );
        $this->flashMessage('Šablona úspěšně editována.', 'success');
        $this->redrawControl('flashesSnippet');
    }

    /**
     * @param int $problemId
     * @param int $difficultyId
     * @throws \Dibi\Exception
     */
    public function handleDifficultyUpdate(int $problemId, int $difficultyId)
    {
        $this->problemPrototypeManager->update($problemId, ['difficulty_id' => $difficultyId]);
        $this['problemGrid']->reload();
        $this->flashMessage('Obtížnost úspěšně změněna.', 'success');
        $this->redrawControl('flashesSnippet');
    }

    /**
     * @param int $problemId
     * @param int $subCategoryId
     * @throws \Dibi\Exception
     */
    public function handleSubCategoryUpdate(int $problemId, int $subCategoryId)
    {
        $this->problemManager->update($problemId, [
            "sub_category_id" => $subCategoryId
        ]);
        $this["problemGrid"]->reload();
        $this->flashMessage("Téma úspěšně změněno.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @param int $problem_id
     * @throws \Dibi\Exception
     */
    public function handleDelete(int $problem_id)
    {
        $this->problemPrototypeManager->delete($problem_id);
        $this['problemGrid']->reload();
        $this->flashMessage('Šablona úspěšně odstraněna.', 'success');
        $this->redrawControl('flashesSnippet');
    }

    /**
     * @param string $structure
     * @param int $conditionType
     * @param int $accessor
     * @param int $problemType
     * @param string $variable
     * @param int $problemId
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handleCondValidation(string $structure, int $conditionType, int $accessor, int $problemType, string $variable, int $problemId = null)
    {
        $form = $problemId ? "problemEditForm" : "problemCreateForm";

        $validationFields["variable"] = $variable;
        $validationFields['structure'] = ArrayHash::from([
            "structure" => $structure,
            "variable" => $variable
        ]);

        $validationErrors = $this->validationService->validate($validationFields);

        //First validate variable and structure of prototype
        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $this[$form][$veKey]->addError($error);
            }
            $this->redrawFormErrors();
            return;
        }

        try{
            $standardized = $this->mathService->standardizeEquation($structure);
        } catch (StringFormatException $e){
            $this[$form]["structure"]->addError($e->getMessage());
            $this->redrawFormErrors();
            return;
        }

        $validationFields = [];

        //Then validate it's type
        $validationFields["type"] = [
            "type_" . $problemType => ArrayHash::from([
                "structure" => $structure,
                "standardized" => $standardized,
                "variable" => $variable
            ])
        ];

        $validationErrors = $this->validationService->validate($validationFields);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $this[$form]["type"]->addError($error);
            }
            $this->redrawFormErrors();
            return;
        }

        $validationFields = [];

        //Then validate specified condition
        $validationFields['condition'] = [
            'condition_' . $conditionType => ArrayHash::from([
                "structure" => $structure,
                "standardized" => $standardized,
                "accessor" => $accessor,
                "variable" => $variable
            ])
        ];

        if(!$problemId){
            //Validate on problem create
            $validationErrors = $this->validationService->validate($validationFields);
        }
        else{
            //Validate on problem edit
            $validationErrors = $this->validationService->editValidate($validationFields, $problemId);
        }

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $this[$form]['condition_' . $conditionType]->addError($error);
            }
        }

        $this->redrawFormErrors();

        //If validation succeeded, return true in payload
        if(!$validationErrors){
            $this->flashMessage("Podmínka je splnitelná.", "success");
            $this->payload->result = true;
        }
    }

    public function redrawFormErrors()
    {
        $this->redrawControl("variableErrorSnippet");
        $this->redrawControl('structureErrorSnippet');
        $this->redrawControl("typeErrorSnippet");
        $this->redrawControl('conditionsErrorSnippet');
        $this->redrawControl("flashesSnippet");
        $this->redrawControl('prototype_create_submitErrorSnippet');
    }

    /**
     * @param $problemTypeId
     * @throws Nette\Application\AbortException
     * @throws \Dibi\Exception
     */
    public function handleGetTypeConditions($problemTypeId)
    {
        $this->sendJson($this->conditionTypeManager->getByProblemType($problemTypeId));
    }
}