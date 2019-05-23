<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.4.19
 * Time: 22:19
 */

namespace App\AdminModule\Presenters;


use App\Components\DataGrids\SuperGroupGridFactory;
use App\Components\Forms\SuperGroupForm\SuperGroupFormControl;
use App\Components\Forms\SuperGroupForm\SuperGroupFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarFactory;
use App\Model\Entity\SuperGroup;
use App\Model\Functionality\SuperGroupFunctionality;
use App\Model\Repository\SuperGroupRepository;
use App\Service\Authorizator;
use App\Service\ValidationService;
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
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param SuperGroupRepository $superGroupRepository
     * @param SuperGroupFunctionality $superGroupFunctionality
     * @param SuperGroupGridFactory $superGroupGridFactory
     * @param SuperGroupFormFactory $superGroupFormFactory
     * @param ValidationService $validationService
     */
    public function __construct
    (
        Authorizator $authorizator,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory,
        SuperGroupRepository $superGroupRepository, SuperGroupFunctionality $superGroupFunctionality,
        SuperGroupGridFactory $superGroupGridFactory, SuperGroupFormFactory $superGroupFormFactory,
        ValidationService $validationService
    )
    {
        parent::__construct($authorizator, $headerBarFactory, $sideBarFactory);
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
        $this->superGroupFunctionality->delete($id);
        $this["superGroupGrid"]->reload();
        $this->flashMessage("Superskupina úspěšně odstraněna.", "success");
        $this->redrawControl("mainFlashesSnippet");
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
        $this->superGroupFunctionality->update($id, $row);
        $this->flashMessage("Superskupina úspěšně editována.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @return SuperGroupFormControl
     */
    public function createComponentSuperGroupCreateForm(): SuperGroupFormControl
    {
        $control = $this->superGroupFormFactory->create();
        $control->onSuccess[] = function (){
            $this['superGroupGrid']->reload();
            $this->informUser('Superskupina úspěšně vytvořena.', true);
        };
        $control->onError[] = function ($e){
            $this->informUser('Chyba při vytváření superskupiny.', true, 'danger');
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
            $this->informUser('Superskupina úspěšně editována.');
            $this->redirect("default");
        };
        $control->onError[] = function ($e){
            $this->informUser('Chyba při editaci superskupiny.', false, 'danger');
            $this->redirect("default");
        };
        return $control;
    }
}