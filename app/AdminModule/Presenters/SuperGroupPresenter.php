<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.4.19
 * Time: 22:19
 */

namespace App\AdminModule\Presenters;


use App\Arguments\UserInformArgs;
use App\Components\DataGrids\SuperGroupGridFactory;
use App\Components\Forms\SuperGroupForm\SuperGroupFormControl;
use App\Components\Forms\SuperGroupForm\SuperGroupFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Model\Entity\SuperGroup;
use App\Model\Functionality\SuperGroupFunctionality;
use App\Model\Repository\SuperGroupRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use App\Services\ValidationService;
use Nette\ComponentModel\IComponent;

/**
 * Class SuperGroupPresenter
 * @package App\AdminModule\Presenters
 */
class SuperGroupPresenter extends AdminPresenter
{
    /**
     * @var SuperGroupRepository
     */
    protected $superGroupRepository;

    /**
     * @var SuperGroupFunctionality
     */
    protected $superGroupFunctionality;

    /**
     * @var SuperGroupGridFactory
     */
    protected $superGroupGridFactory;

    /**
     * @var SuperGroupFormFactory
     */
    protected $superGroupFormFactory;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * SuperGroupPresenter constructor.
     * @param Authorizator $authorizator
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param SuperGroupRepository $superGroupRepository
     * @param SuperGroupFunctionality $superGroupFunctionality
     * @param SuperGroupGridFactory $superGroupGridFactory
     * @param SuperGroupFormFactory $superGroupFormFactory
     * @param ValidationService $validationService
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        SuperGroupRepository $superGroupRepository, SuperGroupFunctionality $superGroupFunctionality,
        SuperGroupGridFactory $superGroupGridFactory, SuperGroupFormFactory $superGroupFormFactory,
        ValidationService $validationService,
        ISectionHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct($authorizator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory);
        $this->superGroupRepository = $superGroupRepository;
        $this->superGroupFunctionality = $superGroupFunctionality;
        $this->superGroupGridFactory = $superGroupGridFactory;
        $this->superGroupFormFactory = $superGroupFormFactory;
        $this->validationService = $validationService;
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function actionEdit(int $id): void
    {
        $record = $this->superGroupRepository->find($id);
        if($this->user->isInRole("teacher") && !$this->authorizator->isSuperGroupAllowed($this->user->identity, $record)){
            $this->flashMessage("Nedostatečná přístupová práva.", "danger");
            $this->redirect("Homepage:default");
        }
        $form = $this["superGroupEditForm"]["form"];
        if(!$form->isSubmitted()){
            $record = $this->superGroupRepository->find($id);
            $this["superGroupEditForm"]->template->entityLabel = $record->getLabel();
            $this->template->entityLabel = $record->getLabel();
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param SuperGroup $record
     */
    private function setDefaults(IComponent $form, SuperGroup $record): void
    {
        $form["id"]->setDefaultValue($record->getId());
        $form["id_hidden"]->setDefaultValue($record->getId());
        $form["label"]->setDefaultValue($record->getLabel());
    }

    /**
     * @param $name
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentSuperGroupGrid($name)
    {
        $grid = $this->superGroupGridFactory->create($this, $name);

        $grid->addAction("delete", "", "delete!")
            ->setIcon("trash")
            ->setClass("btn btn-danger btn-sm ajax");

        $grid->addAction("edit", "", "edit!")
            ->setIcon("edit")
            ->setClass("btn btn-primary btn-sm");

        $grid->addInlineEdit()
            ->setIcon('pencil-alt')
            ->setTitle('Upravit inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = function($container) {
            $container->addText('label', '');
        };

        $grid->getInlineEdit()->onSetDefaults[] = function($cont, $item) {
            $cont->setDefaults([
                "label" => $item->getLabel()
            ]);
        };

        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleInlineUpdate'];
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function handleDelete(int $id): void
    {
        try{
            $this->superGroupFunctionality->delete($id);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('delete', true,'error', $e));
        }
        $this["superGroupGrid"]->reload();
        $this->informUser(new UserInformArgs('delete', true));
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleEdit(int $id): void
    {
        $this->redirect("edit", (int) $id);
    }

    /**
     * @param int $id
     * @param $row
     * @throws \Exception
     */
    public function handleInlineUpdate(int $id, $row): void
    {
        try{
            $this->superGroupFunctionality->update($id, $row);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('edit', true,'error', $e));
        }
        $this->informUser(new UserInformArgs('edit', true));
    }

    /**
     * @return SuperGroupFormControl
     */
    public function createComponentSuperGroupCreateForm(): SuperGroupFormControl
    {
        $control = $this->superGroupFormFactory->create();
        $control->onSuccess[] = function (){
            $this['superGroupGrid']->reload();
            $this->informUser(new UserInformArgs('create', true));
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('create', true,'error', $e));
        };
        return $control;
    }

    /**
     * @return SuperGroupFormControl
     */
    public function createComponentSuperGroupEditForm(): SuperGroupFormControl
    {
        $control = $this->superGroupFormFactory->create(true);
        $control->onSuccess[] = function (){
            $this->informUser(new UserInformArgs('edit'));
            $this->redirect("default");
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('edit', false, 'error', $e));
            $this->redirect("default");
        };
        return $control;
    }
}