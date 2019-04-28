<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 9:40
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\TemplateGridFactory;
use App\Components\Forms\TemplateFormFactory;
use App\Exceptions\StringFormatException;
use App\Helpers\ConstHelper;
use App\Model\Functionality\BaseFunctionality;
use App\Model\Repository\BaseRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Service\MathService;
use App\Service\ValidationService;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class ProblemTemplatePresenter
 * @package App\AdminModule\Presenters
 */
abstract class ProblemTemplatePresenter extends AdminPresenter
{
    /**
     * @var BaseRepository
     */
    protected $repository;

    /**
     * @var BaseFunctionality
     */
    protected $functionality;

    /**
     * @var ProblemTypeRepository
     */
    protected $problemTypeRepository;

    /**
     * @var TemplateGridFactory
     */
    protected $templateGridFactory;

    /**
     * @var TemplateFormFactory
     */
    protected $templateFormFactory;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * @var MathService
     */
    protected $mathService;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var string
     */
    protected $type = "";

    /**
     * @var int
     */
    protected $typeId;

    /**
     * ProblemTemplatePresenter constructor.
     * @param ProblemTypeRepository $problemTypeRepository
     * @param TemplateGridFactory $templateGridFactory
     * @param TemplateFormFactory $templateFormFactory
     * @param ValidationService $validationService
     * @param MathService $mathService
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        ProblemTypeRepository $problemTypeRepository,
        TemplateGridFactory $templateGridFactory, TemplateFormFactory $templateFormFactory,
        ValidationService $validationService, MathService $mathService,
        ConstHelper $constHelper
    )
    {
        parent::__construct();
        $this->problemTypeRepository = $problemTypeRepository;
        $this->templateGridFactory = $templateGridFactory;
        $this->templateFormFactory = $templateFormFactory;
        $this->validationService = $validationService;
        $this->mathService = $mathService;
        $this->constHelper = $constHelper;
    }

    public function actionDefault()
    {
        $types = $this->problemTypeRepository->findAssoc([], "id");
        $this->template->problemTypes = $types;
        $this->template->condByProblemTypes = [];
        foreach ($types as $key => $type)
            $this->template->condByProblemTypes[$key] = $type->getConditionTypes()->getValues();
    }

    /**
     * @param $name
     * @return DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentTemplateGrid($name): DataGrid
    {
        $grid = $this->templateGridFactory->create($this, $name, $this->repository, $this->typeId);
        $grid->addAction('delete', '', 'delete!')
            ->setTemplate(__DIR__ . '/templates/' . $this->type . '/removeBtn.latte');
        $grid->addAction('edit', '', "update!")
            ->setIcon("edit")
            ->setTitle("Editovat šablonu")
            ->setClass("btn btn-primary btn-sm");
        return $grid;
    }

    /**
     * @param int $id
     */
    public function handleDelete(int $id)
    {
        try{
            $this->functionality->delete($id);
        } catch (\Exception $e){
            $this->flashMessage('Chyba při odstraňování šablony.', 'danger');
            $this->redrawControl('mainFlashesSnippet');
            return;
        }
        $this['templateGrid']->reload();
        $this->flashMessage('Šablona úspěšně odstraněna.', 'success');
        $this->redrawControl('mainFlashesSnippet');
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Exception
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values)
    {
        try{
            $this->functionality->create($values);
        } catch (\Exception $e){
            $this->flashMessage("Chyba při vytváření šablony.", "danger");
            $this->redrawControl("mainFlashesSnippet");
            return;
        }
        $this["templateGrid"]->reload();
        $this->flashMessage("Šablona úspěšně vytvořena.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @param Form $form
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handleFormValidate(Form $form)
    {
        $values = $form->getValues();

        //First validate problem body
        $validateFields["variable"] = $values->variable;
        $validateFields["body"] = ArrayHash::from([
            "body" => $values->body,
            "variable" => $values->variable,
            "bodyType" => $this->constHelper::LINEAR_EQ
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
                $standardized = $this->mathService->standardizeEquation($values->body);
            } catch (StringFormatException $e){
                $form["body"]->addError($e->getMessage());
                $this->redrawFormErrors();
                return;
            }
        }

        $validateFields = [];

        //Then validate if the entered problem corresponds to the selected type
        $validateFields["type"] = [
            "type_" . $values->type => ArrayHash::from([
                "body" => $values->body,
                "standardized" => $standardized,
                "variable" => $values->variable
            ])
        ];

        $validationErrors = $this->validationService->validate($validateFields);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form["body"]->addError($error);
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

    public function redrawFormErrors()
    {
        $this->redrawControl("variableErrorSnippet");
        $this->redrawControl('bodyErrorSnippet');
        $this->redrawControl("typeErrorSnippet");
        $this->redrawControl('conditionsErrorSnippet');
        $this->redrawControl("flashesSnippet");
        $this->redrawControl('prototype_create_submitErrorSnippet');
    }

    /**
     * @param string $structure
     * @param int $conditionType
     * @param int $accessor
     * @param int $problemType
     * @param string $variable
     * @param int|null $problemId
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handleCondValidation(string $body, int $conditionType, int $accessor, int $problemType, string $variable, int $problemId = null)
    {
        $form = $problemId ? "templateEditForm" : "templateCreateForm";

        $validationFields["variable"] = $variable;
        $validationFields['body'] = ArrayHash::from([
            "body" => $body,
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

        /*try{
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
        }*/

        $this->redrawFormErrors();

        //If validation succeeded, return true in payload
        if(!$validationErrors){
            $this->flashMessage("Podmínka je splnitelná.", "success");
            $this->payload->result = true;
        }
    }
}