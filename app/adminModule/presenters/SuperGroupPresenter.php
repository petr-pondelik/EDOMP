<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.4.19
 * Time: 22:19
 */

namespace App\AdminModule\Presenters;


use App\Components\DataGrids\SuperGroupGridFactory;
use App\Components\Forms\SuperGroupFormFactory;
use App\Model\Entity\SuperGroup;
use App\Model\Functionality\SuperGroupFunctionality;
use App\Model\Repository\SuperGroupRepository;
use App\Service\ValidationService;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Nette\Utils\ArrayHash;

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
     * @param SuperGroupRepository $superGroupRepository
     * @param SuperGroupFunctionality $superGroupFunctionality
     * @param SuperGroupGridFactory $superGroupGridFactory
     * @param SuperGroupFormFactory $superGroupFormFactory
     * @param ValidationService $validationService
     */
    public function __construct
    (
        SuperGroupRepository $superGroupRepository, SuperGroupFunctionality $superGroupFunctionality,
        SuperGroupGridFactory $superGroupGridFactory, SuperGroupFormFactory $superGroupFormFactory,
        ValidationService $validationService
    )
    {
        parent::__construct();
        $this->superGroupRepository = $superGroupRepository;
        $this->superGroupFunctionality = $superGroupFunctionality;
        $this->superGroupGridFactory = $superGroupGridFactory;
        $this->superGroupFormFactory = $superGroupFormFactory;
        $this->validationService = $validationService;
    }

    /**
     * @param int $id
     */
    public function actionEdit(int $id): void
    {
        $form = $this["superGroupEditForm"];
        if(!$form->isSubmitted()){
            $record = $this->superGroupRepository->find($id);
            $this->template->id = $id;
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
     */
    public function handleInlineUpdate(int $id, $row): void
    {
        $this->superGroupFunctionality->update($id, $row);
        $this->flashMessage("Superskupina úspěšně editována.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    public function createComponentSuperGroupCreateForm(): Form
    {
        $form = $this->superGroupFormFactory->create();
        $form->onValidate[] = [$this, "handleFormValidate"];
        $form->onSuccess[] = [$this, "handleCreateFormSuccess"];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Exception
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values): void
    {
        bdump($values);
        $this->superGroupFunctionality->create($values);
        $this["superGroupGrid"]->reload();
        $this->flashMessage("Super-Skupina úspěšně vytvořena.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @return Form
     */
    public function createComponentSuperGroupEditForm(): Form
    {
        $form = $this->superGroupFormFactory->create();
        $form->addInteger('id', 'ID')
            ->setHtmlAttribute('class', 'form-control')
            ->setDisabled();

        $form->addHidden('id_hidden');
        $form->onValidate[] = [$this, "handleFormValidate"];
        $form->onSuccess[] = [$this, "handleEditFormSuccess"];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Nette\Application\AbortException
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
        $this->superGroupFunctionality->update($values->id_hidden, ArrayHash::from([
            "label" => $values->label
        ]));
        $this->flashMessage('Superskupina úspěšně editována.', 'success');
        $this->redirect("default");
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->values;

        $validateFields["label"] = $values->label;

        $validationErrors = $this->validationService->validate($validateFields);

        bdump($validationErrors);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
            }
        }

        $this->redrawControl("labelErrorSnippet");
    }

}