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
use App\Model\Entities\Logo;
use App\Model\Managers\LogoManager;
use App\Services\FileService;
use App\Services\ValidationService;
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
     * @var LogoManager
     */
    protected $logoManager;

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
     * @param LogoManager $logoManager
     * @param ValidationService $validationService
     * @param FileService $fileService
     * @param LogoGridFactory $logoGridFactory
     * @param LogoFormFactory $logoFormFactory
     */
    public function __construct
    (
        LogoManager $logoManager,
        ValidationService $validationService, FileService $fileService,
        LogoGridFactory $logoGridFactory, LogoFormFactory $logoFormFactory
    )
    {
        parent::__construct();
        $this->logoManager = $logoManager;
        $this->validationService = $validationService;
        $this->fileService = $fileService;
        $this->logoGridFactory = $logoGridFactory;
        $this->logoFormFactory = $logoFormFactory;
    }

    /**
     * @throws \Dibi\Exception
     */
    public function renderDefault()
    {
        $this->logoManager->deleteEmpty();
        $this->fileService->clearLogosTmpDir();
    }

    /**
     * @param $logo_id
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function actionEdit($logo_id)
    {
        $form = $this["logoEditForm"];
        if(!$form->isSubmitted()){
            $record = $this->logoManager->getById((int) $logo_id);
            $this->template->logoId = $logo_id;
            $this->template->path = $record->path;
            $this->template->extension = $record->extension;
            $this->setDefaults($form, $record);
        }
    }

    /**
     * @param IComponent $form
     * @param Logo $record
     */
    public function setDefaults(IComponent $form, Logo $record)
    {
        $form["logo_id"]->setDefaultValue($record->logo_id);
        $form["logo_id_hidden"]->setDefaultValue($record->logo_id);
        $form["label"]->setDefaultValue($record->label);
        $form["logo_file"]->setDefaultValue($record->logo_id);
    }

    /**
     * @param $name
     * @throws \Dibi\NotSupportedException
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

        $grid->addInlineEdit('logo_id')
            ->setIcon('pencil-alt')
            ->setTitle('Upravit inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = function($container) {
            $container->addText('label', '');
        };

        $grid->getInlineEdit()->onSetDefaults[] = function($cont, $item) {
            bdump($item);
            $cont->setDefaults([
                "label" => $item->label
            ]);
        };

        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleInlineEdit'];

        $grid->setItemsDetail(__DIR__ . "/templates/Logo/detail.latte")
            ->setClass("btn btn-sm btn-primary ajax");
    }

    /**
     * @param int $logoId
     * @throws \Dibi\Exception
     */
    public function handleDelete(int $logoId)
    {
        $this->logoManager->delete($logoId);
        $this->fileService->deleteLogoFile($logoId);
        $this["logoGrid"]->reload();
        $this->flashMessage("Logo úspěšně odstraněno.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    public function handleEdit(int $logo_id)
    {
        bdump($logo_id);
        $this->redirect("edit", $logo_id);
    }

    /**
     * @param int $logoId
     * @param $row
     * @throws \Dibi\Exception
     */
    public function handleInlineEdit(int $logoId, $row)
    {
        $this->logoManager->update($logoId, $row);
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
     * @throws \Dibi\Exception
     */
    public function handleLogoCreateFormSuccess(Form $form, ArrayHash $values)
    {
        bdump("TEST");
        bdump($values);
        if($values->logo_file) {
            $this->fileService->finalStore($values->logo_file);
            $this->logoManager->update($values->logo_file, [
                "label" => $values->label
            ]);
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
        $form->addInteger("logo_id", "ID")
            ->setHtmlAttribute("class", "form-control")
            ->setDisabled();
        $form->addSelect("edit_logo", "Editovat soubor", [
            0 => "Ne",
            1 => "Ano"
        ])
            ->setHtmlAttribute("class", "form-control mb-3");
        $form->addHidden("logo_id_hidden");
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
     * @throws \Dibi\Exception
     * @throws \Nette\Application\AbortException
     */
    public function handleLogoEditFormSuccess(Form $form, ArrayHash $values)
    {
        //TODO: Edit logo record and logo file depending on the edit_logo and logo_file values

        bdump($values);
        if($values->edit_logo && $values->logo_file){
            $this->fileService->finalStore($values->logo_file);
        }
        $this->logoManager->update($values->logo_id_hidden, [
            "label" => $values->label
        ]);

        $this->flashMessage("Logo úspěšně editováno.", "success");
        $this->redirect("default");
    }

    /**
     * @throws \Dibi\Exception
     * @throws \Nette\Application\AbortException
     */
    public function handleUploadFile()
    {
        $this->sendResponse( new TextResponse($this->fileService->uploadFile($this->getHttpRequest())) );
    }

    /**
     * @throws \Dibi\Exception
     * @throws \Nette\Application\AbortException
     */
    public function handleRevertFileUpload()
    {
        $this->sendResponse( new TextResponse($this->fileService->revertFileUpload($this->getHttpRequest())) );
    }

    /**
     * @throws \Dibi\Exception
     * @throws \Nette\Application\AbortException
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