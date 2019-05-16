<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 13.4.19
 * Time: 18:05
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\LogoGridFactory;
use App\Components\Forms\LogoFormFactory;
use App\Model\Entity\Logo;
use App\Model\Functionality\LogoFunctionality;
use App\Model\Repository\LogoRepository;
use App\Service\FileService;
use App\Service\ValidationService;
use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Nette\Utils\ArrayHash;

/**
 * Class LogoPresenter
 * @package App\Presenters
 */
class LogoPresenter extends AdminPresenter
{
    /**
     * @var LogoRepository
     */
    protected $logoRepository;

    /**
     * @var LogoFunctionality
     */
    protected $logoFunctionality;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * @var LogoGridFactory
     */
    protected $logoGridFactory;

    /**
     * @var LogoFormFactory
     */
    protected $logoFormFactory;

    /**
     * SettingsPresenter constructor.
     * @param LogoRepository $logoRepository
     * @param LogoFunctionality $logoFunctionality
     * @param ValidationService $validationService
     * @param FileService $fileService
     * @param LogoGridFactory $logoGridFactory
     * @param LogoFormFactory $logoFormFactory
     */
    public function __construct
    (
        LogoRepository $logoRepository, LogoFunctionality $logoFunctionality,
        ValidationService $validationService, FileService $fileService,
        LogoGridFactory $logoGridFactory, LogoFormFactory $logoFormFactory
    )
    {
        parent::__construct();
        $this->logoRepository = $logoRepository;
        $this->logoFunctionality = $logoFunctionality;
        $this->validationService = $validationService;
        $this->fileService = $fileService;
        $this->logoGridFactory = $logoGridFactory;
        $this->logoFormFactory = $logoFormFactory;
    }

    public function renderDefault()
    {
        $this->logoFunctionality->deleteEmpty();
        $this->fileService->clearLogosTmpDir();
    }

    /**
     * @param $id
     */
    public function actionEdit($id)
    {
        $form = $this["logoEditForm"];
        if(!$form->isSubmitted()){
            $record = $this->logoRepository->find((int) $id);
            $this->template->id = $id;
            $this->template->path = $record->getPath();
            $this->template->extension = $record->getExtension();
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param Logo $record
     */
    public function setDefaults(IComponent $form, Logo $record)
    {
        $form["id"]->setDefaultValue($record->getId());
        $form["id_hidden"]->setDefaultValue($record->getId());
        $form["label"]->setDefaultValue($record->getLabel());
        $form["logo_file"]->setDefaultValue($record->getId());
    }

    /**
     * @param $name
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentLogoGrid($name)
    {
        $grid = $this->logoGridFactory->create($this, $name);

        $grid->addAction("delete", "", "delete!")
            ->setTemplate(__DIR__ . "/templates/Logo/delete_action.latte");

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
            bdump($item);
            $cont->setDefaults([
                "label" => $item->getLabel()
            ]);
        };

        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleInlineUpdate'];

        $grid->setItemsDetail(__DIR__ . "/templates/Logo/detail.latte")
            ->setClass("btn btn-sm btn-primary ajax");
    }

    /**
     * @param int $logoId
     * @throws \Exception
     */
    public function handleDelete(int $logoId)
    {
        $this->logoFunctionality->delete($logoId);
        $this->fileService->deleteLogoFile($logoId);
        $this["logoGrid"]->reload();
        $this->flashMessage("Logo úspěšně odstraněno.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleEdit(int $id)
    {
        $this->redirect("edit", $id);
    }

    /**
     * @param int $logoId
     * @param $row
     */
    public function handleInlineUpdate(int $logoId, $row)
    {
        try{
            $this->logoFunctionality->update($logoId, $row);
        } catch (\Exception $e){
            $this->flashMessage("Chyba při editaci loga.", "danger");
            $this->redrawControl("mainFlashesSnippet");
        }
        $this->flashMessage("Logo úspěšně editováno.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @return Form
     */
    public function createComponentLogoCreateForm()
    {
        $form = $this->logoFormFactory->create();
        $form->onValidate[] = [$this, "handleCreateFormValidate"];
        $form->onSuccess[] = [$this, "handleLogoCreateFormSuccess"];
        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleCreateFormValidate(Form $form)
    {
        $values = $form->values;

        $validateFields["label"] = $values->label;
        $validateFields["logo_file"] = $values->logo_file;

        $validationErrors = $this->validationService->validate($validateFields);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
            }
        }

        $this->redrawControl("labelErrorSnippet");
        $this->redrawControl("logoFileErrorSnippet");
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Exception
     */
    public function handleLogoCreateFormSuccess(Form $form, ArrayHash $values)
    {
        bdump("TEST");
        bdump($values);
        if($values->logo_file) {
            $this->fileService->finalStore($values->logo_file);
            $this->logoFunctionality->update($values->logo_file, ArrayHash::from([
                "label" => $values->label
            ]));
        }
        $this["logoGrid"]->reload();
        $this->flashMessage("Logo úspěšně vytvořeno.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @return Form
     */
    public function createComponentLogoEditForm()
    {
        $form = $this->logoFormFactory->create();
        $form->addInteger("id", "ID")
            ->setHtmlAttribute("class", "form-control")
            ->setDisabled();
        $form->addSelect("edit_logo", "Editovat soubor", [
            0 => "Ne",
            1 => "Ano"
        ])
            ->setHtmlAttribute("class", "form-control mb-3");
        $form->addHidden("id_hidden");
        $form->onValidate[] = [$this, "handleEditFormValidate"];
        $form->onSuccess[] = [$this, "handleLogoEditFormSuccess"];
        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleEditFormValidate(Form $form)
    {
        $values = $form->values;

        $validateFields["label"] = $values->label;

        if($values->edit_logo)
            $validateFields["logo_file"] = $values->logo_file;

        $validationErrors = $this->validationService->validate($validateFields);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
            }
        }

        $this->redrawControl("labelErrorSnippet");
        $this->redrawControl("logoFileErrorSnippet");
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Nette\Application\AbortException
     */
    public function handleLogoEditFormSuccess(Form $form, ArrayHash $values)
    {
        //TODO: Edit logo record and logo file depending on the edit_logo and logo_file values

        bdump($values);
        if($values->edit_logo && $values->logo_file){
            $this->fileService->finalStore($values->logo_file);
        }
        $this->logoFunctionality->update($values->id_hidden, ArrayHash::from([
            "label" => $values->label
        ]));

        $this->flashMessage("Logo úspěšně editováno.", "success");
        $this->redirect("default");
    }

    /**
     * @throws \Nette\Application\AbortException
     * @throws \Exception
     */
    public function handleUploadFile()
    {
        $this->sendResponse( new TextResponse($this->fileService->uploadFile($this->getHttpRequest())) );
    }

    /**
     * @throws \Nette\Application\AbortException
     * @throws \Exception
     */
    public function handleRevertFileUpload()
    {
        $this->sendResponse( new TextResponse($this->fileService->revertFileUpload($this->getHttpRequest())) );
    }

    /**
     * @throws \Nette\Application\AbortException
     * @throws \Exception
     */
    public function handleUpdateFile()
    {
        $this->sendResponse( new TextResponse($this->fileService->updateFile($this->getHttpRequest())) );
    }

    /**
     * @throws \Nette\Application\AbortException
     */
    public function handleRevertFileUpdate()
    {
        $this->sendResponse( new TextResponse($this->fileService->revertFileUpdate($this->getHttpRequest())) );
    }
}