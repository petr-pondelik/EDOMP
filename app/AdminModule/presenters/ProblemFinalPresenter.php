<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 17:18
 */

namespace App\AdminModule\Presenters;

use App\Arguments\UserInformArgs;
use App\Components\DataGrids\ProblemGridFactory;
use App\Components\Forms\ProblemFinalForm\ProblemFinalFormControl;
use App\Components\Forms\ProblemFinalForm\ProblemFinalFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Model\Entity\ProblemFinal;
use App\Model\Functionality\ProblemFinalFunctionality;
use App\Model\Repository\ProblemFinalRepository;
use App\Service\Authorizator;
use App\Service\MathService;
use App\Service\ValidationService;
use Nette\ComponentModel\IComponent;
use Nette\Utils\ArrayHash;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class ProblemFinalPresenter
 * @package App\AdminModule\Presenters
 */
class ProblemFinalPresenter extends AdminPresenter
{
    /**
     * @var ProblemGridFactory
     */
    protected $problemGridFactory;

    /**
     * @var ProblemFinalFormFactory
     */
    protected $problemFinalFormFactory;

    /**
     * @var ProblemFinalRepository
     */
    protected $problemRepository;

    /**
     * @var ProblemFinalFunctionality
     */
    protected $problemFunctionality;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * @var MathService
     */
    protected $mathService;

    /**
     * ProblemFinalPresenter constructor.
     * @param Authorizator $authorizator
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param ProblemGridFactory $problemGridFactory
     * @param ProblemFinalFormFactory $problemFinalFormFactory
     * @param ProblemFinalRepository $problemRepository
     * @param ProblemFinalFunctionality $problemFunctionality
     * @param ValidationService $validationService
     * @param MathService $mathService
     */
    public function __construct
    (
        Authorizator $authorizator,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        ProblemGridFactory $problemGridFactory, ProblemFinalFormFactory $problemFinalFormFactory,
        ProblemFinalRepository $problemRepository, ProblemFinalFunctionality $problemFunctionality,
        ValidationService $validationService, MathService $mathService
    )
    {
        parent::__construct($authorizator, $headerBarFactory, $sideBarFactory, $flashesTranslator);
        $this->problemGridFactory = $problemGridFactory;
        $this->problemFinalFormFactory = $problemFinalFormFactory;
        $this->problemRepository = $problemRepository;
        $this->problemFunctionality = $problemFunctionality;
        $this->validationService = $validationService;
        $this->mathService = $mathService;
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function actionEdit(int $id)
    {
        $form = $this['problemFinalEditForm']['form'];
        if(!$form->isSubmitted()){
            $record = $this->problemRepository->find($id);
            $this->template->id = $id;
            $this['problemFinalEditForm']->template->id = $id;
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param ProblemFinal $record
     */
    private function setDefaults(IComponent $form, ProblemFinal $record): void
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
            ->setTemplate(__DIR__ . '/templates/ProblemFinal/removeBtn.latte');

        $grid->addAction('getResult', 'Získat výsledek')
            ->setTitle('Získat výsledek')
            ->setTemplate(__DIR__ . '/templates/ProblemFinal/getResultBtn.latte');

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
    public function handleDelete(int $id): void
    {
        try{
            $this->problemFunctionality->delete($id);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('delete', true, 'error', $e));
            return;
        }
        $this['problemGrid']->reload();
        $this->informUser(new UserInformArgs('delete', true));
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
            $this->problemFunctionality->update($id, $data, false);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('edit', true, 'error', $e));
        }
        $this->informUser(new UserInformArgs('edit', true));
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
                ArrayHash::from(["subcategory" => $subCategoryId]),false
            );
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('subCategory', true, 'error', $e));
            return;
        }
        $this["problemGrid"]->reload();
        $this->informUser(new UserInformArgs('subCategory', true));
    }

    /**
     * @param int $problemId
     * @param int $difficultyId
     */
    public function handleDifficultyUpdate(int $problemId, int $difficultyId)
    {
        try{
            $this->problemFunctionality->update($problemId,
                ArrayHash::from(['difficulty' => $difficultyId]), false
            );
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('difficulty', true, 'error', $e));
            return;
        }
        $this['problemGrid']->reload();
        $this->informUser(new UserInformArgs('difficulty', true));
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function handleGetResult(int $id)
    {
        $problem = $this->problemRepository->find($id);
        $result = null;
        try{
            $result = $this->mathService->evaluate[(int) $problem->getProblemType()->getId()]($problem);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('getRes', true, 'error', $e));
        }
        $this->problemFunctionality->storeResult($id, $result);
        $this["problemGrid"]->reload();
        $this->informUser(new UserInformArgs('getRes', true));
    }

    /**
     * @return ProblemFinalFormControl
     */
    public function createComponentProblemFinalCreateForm(): ProblemFinalFormControl
    {
        $control = $this->problemFinalFormFactory->create();
        $control->onSuccess[] = function (){
            $this['problemGrid']->reload();
            $this->informUser(new UserInformArgs('create'));
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('create', true, 'error', $e));
        };
        return $control;
    }

    /**
     * @return ProblemFinalFormControl
     */
    public function createComponentProblemFinalEditForm(): ProblemFinalFormControl
    {
        $control= $this->problemFinalFormFactory->create(true);
        $control->onSuccess[] = function (){
            $this->informUser(new UserInformArgs('edit'));
            $this->redirect('default');
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('edit', false, 'error', $e));
            $this->redirect('default');
        };
        return $control;
    }
}