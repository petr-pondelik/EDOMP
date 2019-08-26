<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 9:40
 */

namespace App\AdminModule\Presenters;

use App\Arguments\UserInformArgs;
use App\Components\DataGrids\TemplateGridFactory;
use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormControl;
use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\ConstHelper;
use App\Helpers\FlashesTranslator;
use App\Model\Persistent\Entity\ProblemTemplate;
use App\Model\Persistent\Functionality\BaseFunctionality;
use App\Model\Persistent\Repository\BaseRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use Nette\ComponentModel\IComponent;
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
     * @var TemplateGridFactory
     */
    protected $templateGridFactory;

    /**
     * @var ProblemTemplateFormFactory
     */
    protected $problemTemplateFormFactory;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var int
     */
    protected $typeId;

    /**
     * ProblemTemplatePresenter constructor.
     * @param Authorizator $authorizator
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param TemplateGridFactory $templateGridFactory
     * @param ConstHelper $constHelper
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        TemplateGridFactory $templateGridFactory,
        ConstHelper $constHelper, ISectionHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct($authorizator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory);
        $this->templateGridFactory = $templateGridFactory;
        $this->constHelper = $constHelper;
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function actionEdit(int $id): void
    {
        $form = $this['problemTemplateEditForm']['form'];
        if(!$form->isSubmitted()){
            $record = $this->repository->find($id);
            $this->template->id = $id;
            $this['problemTemplateEditForm']->template->id = $id;
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param ProblemTemplate $record
     */
    protected function setDefaults(IComponent $form, ProblemTemplate $record): void
    {
        $form['id']->setDefaultValue($record->getId());
        $form['idHidden']->setDefaultValue($record->getId());
        $form['subCategory']->setDefaultValue($record->getSubCategory()->getId());
        $form['textBefore']->setDefaultValue($record->getTextBefore());
        $form['body']->setDefaultValue($record->getBody());
        $form['textAfter']->setDefaultValue($record->getTextAfter());
        $form['difficulty']->setDefaultVAlue($record->getDifficulty()->getId());

        $conditions = $record->getConditions()->getValues();

        foreach($conditions as $condition){
            $form['condition_' . $condition->getProblemConditionType()->getId()]->setValue($condition->getAccessor());
        }
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
            ->setIcon('trash')
            ->setTitle('Odstranit šablonu')
            ->setClass('btn btn-sm btn-danger ajax');

        $grid->addAction('edit', '', 'update!')
            ->setIcon('edit')
            ->setTitle('Editovat šablonu')
            ->setClass('btn btn-primary btn-sm');

        $grid->addInlineEdit()
            ->setIcon('pencil-alt')
            ->setTitle('Upravit inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = static function($container) {
            $container->addText('textBefore', '');
            $container->addText('textAfter', '');
        };

        $grid->getInlineEdit()->onSetDefaults[] = static function($cont, $item) {
            $cont->setDefaults([
                'textBefore' => $item->getTextBefore(),
                'textAfter' => $item->getTextAfter(),
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
            $this->functionality->delete($id);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('delete', true, 'error', $e, true));
            return;
        }
        $this['templateGrid']->reload();
        $this->informUser(new UserInformArgs('delete', true, 'success', null, true));
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleUpdate(int $id): void
    {
        $this->redirect('edit', $id);
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     */
    public function handleInlineUpdate(int $id, ArrayHash $data): void
    {
        try{
            $this->functionality->update($id, $data, true);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('edit', true, 'error', $e));
        }
        $this->informUser(new UserInformArgs('edit', true));
    }

    /**
     * @param int $subCategoryId
     * @param int $templateId
     */
    public function handleSubCategoryUpdate(int $templateId, int $subCategoryId): void
    {
        try{
            $this->functionality->update($templateId,
                ArrayHash::from(['subcategory' => $subCategoryId]), true
            );
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('subCategory', true, 'error', $e, true));
        }
        $this['templateGrid']->reload();
        $this->informUser(new UserInformArgs('subCategory', true, 'success', null, true));
    }

    /**
     * @param int $templateId
     * @param int $difficultyId
     */
    public function handleDifficultyUpdate(int $templateId, int $difficultyId): void
    {
        try{
            $this->functionality->update($templateId,
                ArrayHash::from(['difficulty' => $difficultyId]), true
            );
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('difficulty', true, 'error', $e, true));
            return;
        }
        $this['templateGrid']->reload();
        $this->informUser(new UserInformArgs('difficulty', true, 'success', null, true));
    }

    /**
     * @return ProblemTemplateFormControl
     */
    public function createComponentProblemTemplateCreateForm(): ProblemTemplateFormControl
    {
        $control = $this->problemTemplateFormFactory->create($this->functionality);
        $control->onSuccess[] = function () {
            $this['templateGrid']->reload();
            $this['problemTemplateCreateForm']->restoreDefaults();
            $this->redrawControl('problemTemplateCreateFormSnippet');
            $this->informUser(new UserInformArgs('create', true, 'success', null, true));
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('create', true, 'error', $e, true));
        };
        return $control;
    }

    /**
     * @return ProblemTemplateFormControl
     */
    public function createComponentProblemTemplateEditForm(): ProblemTemplateFormControl
    {
        $control = $this->problemTemplateFormFactory->create($this->functionality, true);
        $control->onSuccess[] = function () {
            $this->informUser(new UserInformArgs('edit', true, 'success', null, true));
            $this->redirect('default');
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('edit', true, 'error', $e, true));
            $this->redirect('default');
        };
        return $control;
    }

    /**
     * @param array $data
     * @param int $problemId
     */
    public function handleCondValidation(array $data, $problemId = null): void
    {
        bdump($data);
        $form = $problemId ? 'problemTemplateEditForm' : 'problemTemplateCreateForm';
        $this[$form]->handleCondValidation($data, $problemId);
    }
}