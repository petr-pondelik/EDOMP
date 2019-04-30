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
use App\Helpers\ConstHelper;
use App\Model\Entity\ProblemFinal;
use App\Model\Functionality\ProblemFunctionality;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemFinalRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Service\ValidationService;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Nette\Utils\ArrayHash;
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
     * @var ProblemFinalRepository
     */
    protected $problemRepository;

    /**
     * @var ProblemFunctionality
     */
    protected $problemFunctionality;

    /**
     * @var ProblemTypeRepository
     */
    protected $problemTypeRepository;

    /**
     * @var ProblemConditionRepository
     */
    protected $problemConditionRepository;

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
     * @param ProblemGridFactory $problemGridFactory
     * @param ProblemFormFactory $problemFormFactory
     * @param ProblemFinalRepository $problemRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param ProblemFunctionality $problemFunctionality
     * @param ProblemConditionRepository $problemConditionRepository
     * @param ValidationService $validationService
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        ProblemGridFactory $problemGridFactory, ProblemFormFactory $problemFormFactory,
        ProblemFinalRepository $problemRepository, ProblemTypeRepository $problemTypeRepository,
        ProblemFunctionality $problemFunctionality,
        ProblemConditionRepository $problemConditionRepository,
        ValidationService $validationService,
        ConstHelper $constHelper
    )
    {
        parent::__construct();
        $this->problemGridFactory = $problemGridFactory;
        $this->problemFormFactory = $problemFormFactory;
        $this->problemRepository = $problemRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->problemFunctionality = $problemFunctionality;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->validationService = $validationService;
        $this->constHelper = $constHelper;
    }

    /**
     * @throws \Exception
     */
    public function actionDefault()
    {
        $types = $this->problemTypeRepository->findAssoc([], "id");
        $this->template->problemTypes = $types;
        $this->template->condByProblemTypes = [];
        foreach ($types as $key => $type)
            $this->template->condByProblemTypes[$key] = $type->getConditionTypes()->getValues();
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function actionEdit(int $id)
    {
        $this->actionDefault();
        $form = $this["problemEditForm"];
        if(!$form->isSubmitted()){
            $record = $this->problemRepository->find($id);
            $this->template->problemId = $id;
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param ProblemFinal $record
     */
    private function setDefaults(IComponent $form, ProblemFinal $record)
    {
        $form["id"]->setDefaultValue($record->getId());
        $form["id_hidden"]->setDefaultValue($record->getId());
        $form["text_before"]->setDefaultValue($record->getTextBefore());
        $form["body"]->setDefaultValue($record->getBody());
        $form["text_after"]->setDefaultValue($record->getTextAfter());
        $form["result"]->setDefaultValue($record->getResult());

        $form["type"]->setDefaultValue($record->getProblemType()->getId());
        $form["difficulty"]->setDefaultValue($record->getDifficulty()->getId());
        $form["subcategory"]->setDefaultValue($record->getSubCategory()->getId());

        $conditions = $record->getConditions()->getValues();
        foreach($conditions as $condition)
            $form['condition_' . $condition->getProblemConditionType()->getId()]->setValue($condition->getAccessor());
    }

    /**
     * @param $name
     * @return DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentProblemGrid($name): DataGrid
    {
        $grid = $this->problemGridFactory->create($this, $name);

        $grid->addAction('delete', '', 'delete!')
            ->setTemplate(__DIR__ . '/templates/Problem/removeBtn.latte');

        $grid->addAction('getResult', 'Získat výsledek')
            ->setTitle('Získat výsledek')
            ->setTemplate(__DIR__ . '/templates/Problem/getResultBtn.latte');

        $grid->addAction('edit', "", "update!")
            ->setIcon("edit")
            ->setTitle('Editovat')
            ->setClass("btn btn-primary btn-sm");

        $grid->addInlineEdit()
            ->setIcon('pencil-alt')
            ->setTitle('Upravit inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = function($container) {
            $container->addText('text_before', '');
            $container->addText('body', '');
            $container->addText('text_after', '');
            $container->addText('result', '');
        };

        $grid->getInlineEdit()->onSetDefaults[] = function($cont, $item) {
            bdump($item);
            $cont->setDefaults([
                'text_before' => $item->getTextBefore(),
                'body' => $item->getBody(),
                'text_after' => $item->getTextAfter(),
                'result' => $item->getResult()
            ]);
        };

        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleInlineUpdate'];

        return $grid;
    }

    /**
     * @param int $id
     */
    public function handleDelete(int $id)
    {
        bdump($id);
        try{
            $this->problemFunctionality->delete($id);
        } catch (\Exception $e){
            $this->flashMessage('Chyba při odstraňování příkladu.', 'danger');
            $this->redrawControl('mainFlashesSnippet');
            return;
        }
        $this['problemGrid']->reload();
        $this->flashMessage('Příklad úspěšně odstraněn.', 'success');
        $this->redrawControl('mainFlashesSnippet');
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleUpdate(int $id): void
    {
        $this->redirect("edit", $id);
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     */
    public function handleInlineUpdate(int $id, ArrayHash $data): void
    {
        try{
            $this->problemFunctionality->update($id, $data);
        } catch (\Exception $e){
            $this->flashMessage('Chyba při editaci příkladu.', 'danger');
            $this->redrawControl('mainFlashesSnippet');
            return;
        }
        $this->flashMessage('Příklad úspěšně editován.', 'success');
        $this->redrawControl('mainFlashesSnippet');
    }

    /**
     * @param int $problemId
     * @param int $subCategoryId
     * @throws \Exception
     */
    public function handleSubCategoryUpdate(int $problemId, int $subCategoryId)
    {
        try{
            $this->problemFunctionality->update($problemId,
                ArrayHash::from([
                    "subcategory" => $subCategoryId
            ]),false);
        } catch (\Exception $e){
            $this->flashMessage("Chyba při změně tématu.", "danger");
            $this->redrawControl("mainFlashesSnippet");
            return;
        }
        $this["problemGrid"]->reload();
        $this->flashMessage("Téma úspěšně změněno.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @param int $problemId
     * @param int $difficultyId
     */
    public function handleDifficultyUpdate(int $problemId, int $difficultyId)
    {
        try{
        $this->problemFunctionality->update($problemId,
            ArrayHash::from([
                'difficulty' => $difficultyId
            ]), false);
        } catch (\Exception $e){
            $this->flashMessage("Chyba při změně obtížnosti.", "danger");
            $this->redrawControl("mainFlashesSnippet");
            return;
        }
        $this['problemGrid']->reload();
        $this->flashMessage('Obtížnost úspěšně změněna.', 'success');
        $this->redrawControl('mainFlashesSnippet');
    }

    /*public function handleGetResult(int $id)
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
    }*/

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentProblemCreateForm(): Form
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
     * @param ArrayHash $values
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values)
    {
        try{
            $this->problemFunctionality->create($values);
        } catch (\Exception $e){
            $this->flashMessage('Chyba při vytváření příkladu..', 'danger');
            $this->redrawControl('mainFlashesSnippet');
            return;
        }
        $this['problemGrid']->reload();
        $this->flashMessage('Příklad úspěšně vytvořen.', 'success');
        $this->redrawControl('mainFlashesSnippet');
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentProblemEditForm(): Form
    {
        $form = $this->problemFormFactory->create();
        $form->addText('id', 'ID')
            ->setDisabled()
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('problem-id');
        $form->addHidden('id_hidden');
        $form->addTextArea('result', 'Výsledek')
            ->setHtmlAttribute('class', 'form-control');
        $form->onValidate[] = [$this, 'handleFormValidate'];
        $form->onSuccess[] = [$this, "handleEditFormSuccess"];
        return $form;
    }

    public function handleEditFormSuccess(Form $form, ArrayHash $values)
    {
        try{
            $this->problemFunctionality->update($values->id_hidden, $values);
        } catch (\Exception $e){
            $this->flashMessage('Chyba při editaci příkladu.', 'danger');
            $this->redirect("default");
        }
        $this->flashMessage('Příklad úspěšně editován.', 'success');
        $this->redirect('default');
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form)
    {
        $values = $form->getValues();

        //First validate problem structure
        $validateFields["body"] = ArrayHash::from([
            "body" => $values->body,
            "bodyType" => $this->constHelper::BODY_FINAL
        ]);
        $validationErrors = $this->validationService->validate($validateFields);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
                $this->redrawControl($veKey.'ErrorSnippet');
            }
        }

        $this->redrawControl("flashesSnippet");
        $this->redrawControl('bodyErrorSnippet');
    }

}