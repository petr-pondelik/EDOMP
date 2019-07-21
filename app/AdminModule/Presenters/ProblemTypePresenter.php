<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 31.5.19
 * Time: 21:13
 */

namespace App\AdminModule\Presenters;

use App\Arguments\UserInformArgs;
use App\Components\DataGrids\ProblemTypeGridFactory;
use App\Components\Forms\ProblemTypeForm\ProblemTypeFormControl;
use App\Components\Forms\ProblemTypeForm\ProblemTypeFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Model\Entity\ProblemType;
use App\Model\Functionality\ProblemTypeFunctionality;
use App\Model\Repository\ProblemTypeRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use Nette\ComponentModel\IComponent;
use Nette\Utils\ArrayHash;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class ProblemTypePresenter
 * @package App\AdminModule\Presenters
 */
class ProblemTypePresenter extends AdminPresenter
{
    /**
     * @var ProblemTypeRepository
     */
    protected $problemTypeRepository;

    /**
     * @var ProblemTypeFunctionality
     */
    protected $problemTypeFunctionality;

    /**
     * @var ProblemTypeGridFactory
     */
    protected $problemTypeGridFactory;

    /**
     * @var ProblemTypeFormFactory
     */
    protected $problemTypeFormFactory;

    /**
     * ProblemTypePresenter constructor.
     * @param Authorizator $authorizator
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param ProblemTypeRepository $problemTypeRepository
     * @param ProblemTypeFunctionality $problemTypeFunctionality
     * @param ProblemTypeGridFactory $problemTypeGridFactory
     * @param ProblemTypeFormFactory $problemTypeFormFactory
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        ProblemTypeRepository $problemTypeRepository, ProblemTypeFunctionality $problemTypeFunctionality,
        ProblemTypeGridFactory $problemTypeGridFactory, ProblemTypeFormFactory $problemTypeFormFactory,
        ISectionHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct($authorizator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory);
        $this->problemTypeRepository = $problemTypeRepository;
        $this->problemTypeFunctionality = $problemTypeFunctionality;
        $this->problemTypeGridFactory = $problemTypeGridFactory;
        $this->problemTypeFormFactory = $problemTypeFormFactory;
    }

    /**
     * @param int $id
     */
    public function actionEdit(int $id)
    {
        $form = $this["problemTypeEditForm"]["form"];
        if(!$form->isSubmitted()){
            $record = $this->problemTypeRepository->find($id);
            $this["problemTypeEditForm"]->template->entityLabel = $record->getLabel();
            $this->template->entityLabel = $record->getLabel();
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param ProblemType $record
     */
    private function setDefaults(IComponent $form, ProblemType $record)
    {
        $form["id"]->setDefaultValue($record->getId());
        $form["idHidden"]->setDefaultValue($record->getId());
        $form["label"]->setDefaultValue($record->getLabel());
    }

    /**
     * @param $name
     * @return DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentProblemTypeGrid($name): DataGrid
    {
        $grid = $this->problemTypeGridFactory->create($this, $name);

        $grid->addAction('delete', '', 'delete!')
            ->setTemplate(__DIR__ . '/templates/ProblemType/deleteAction.latte');
            /*->setIcon('trash')
            ->setClass('btn btn-danger btn-sm ajax')
            ->setTitle("Odstranit kategorii.");*/

        $grid->addAction("update", "", "update!")
            ->setIcon("edit")
            ->setClass("btn btn-primary btn-sm")
            ->setTitle("Editovat kategorii.");

        $grid->addInlineEdit()
            ->setIcon('pencil-alt')
            ->setTitle('Upravit inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = static function($container) {
            $container->addText('label', '');
            };

        $grid->getInlineEdit()->onSetDefaults[] = function($cont, ProblemType $item) {
            $cont->setDefaults([ "label" => $item->getLabel() ]);
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
            $this->problemTypeFunctionality->delete($id);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('delete', true,'error', $e));
            return;
        }
        $this['problemTypeGrid']->reload();
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
            $this->problemTypeFunctionality->update($id, $data);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('edit', true,'error', $e));
        }
        $this->informUser(new UserInformArgs('edit', true));
    }

    /**
     * @return ProblemTypeFormControl
     */
    public function createComponentProblemTypeCreateForm(): ProblemTypeFormControl
    {
        $control = $this->problemTypeFormFactory->create();
        $control->onSuccess[] = function (){
            $this['problemTypeGrid']->reload();
            $this->informUser(new UserInformArgs('create', true));
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('create', true,'error', $e));
        };
        return $control;
    }

    /**
     * @return ProblemTypeFormControl
     */
    public function createComponentProblemTypeEditForm(): ProblemTypeFormControl
    {
        $control = $this->problemTypeFormFactory->create(true);
        $control->onSuccess[] = function (){
            $this->informUser(new UserInformArgs('edit'));
            $this->redirect('default');
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('edit', false,'error', $e));
            $this->redirect('default');
        };
        return $control;
    }
}