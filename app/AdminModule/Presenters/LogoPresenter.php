<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 13.4.19
 * Time: 18:05
 */

namespace App\AdminModule\Presenters;

use App\Arguments\UserInformArgs;
use App\Components\DataGrids\LogoGridFactory;
use App\Components\Forms\LogoForm\LogoFormControl;
use App\Components\Forms\LogoForm\LogoFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Model\Entity\Logo;
use App\Model\Functionality\LogoFunctionality;
use App\Model\Repository\LogoRepository;
use App\Services\Authorizator;
use App\Services\FileService;
use App\Services\ValidationService;
use Nette\Application\Responses\TextResponse;
use Nette\ComponentModel\IComponent;

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
     * @param Authorizator $authorizator
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param LogoRepository $logoRepository
     * @param LogoFunctionality $logoFunctionality
     * @param ValidationService $validationService
     * @param FileService $fileService
     * @param LogoGridFactory $logoGridFactory
     * @param LogoFormFactory $logoFormFactory
     */
    public function __construct
    (
        Authorizator $authorizator,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        LogoRepository $logoRepository, LogoFunctionality $logoFunctionality,
        ValidationService $validationService, FileService $fileService,
        LogoGridFactory $logoGridFactory, LogoFormFactory $logoFormFactory
    )
    {
        parent::__construct($authorizator, $headerBarFactory, $sideBarFactory, $flashesTranslator);
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
        $form = $this['logoEditForm']['form'];
        if(!$form->isSubmitted()){
            $record = $this->logoRepository->find((int) $id);
            $this->template->entityLabel = $record->getLabel();
            $this['logoEditForm']->template->entityLabel = $record->getLabel();
            $this['logoEditForm']->template->path = $record->getPath();
            $this['logoEditForm']->template->extension = $record->getExtension();
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
        try{
            $this->logoFunctionality->delete($logoId);
            $this->fileService->deleteLogoFile($logoId);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('delete', true, 'error', $e));
        }
        $this["logoGrid"]->reload();
        $this->informUser(new UserInformArgs('delete', true));
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
            $this->informUser(new UserInformArgs('edit', true, 'error', $e));
        }
        $this->informUser(new UserInformArgs('edit', true));
    }

    /**
     * @return LogoFormControl
     */
    public function createComponentLogoCreateForm(): LogoFormControl
    {
        $control = $this->logoFormFactory->create();
        $control->onSuccess[] = function (){
            $this['logoGrid']->reload();
            $this->informUser(new UserInformArgs('create', true));
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('create', true, 'error', $e));
        };
        return $control;
    }

    /**
     * @return LogoFormControl
     */
    public function createComponentLogoEditForm(): LogoFormControl
    {
        $control = $this->logoFormFactory->create(true);
        $control->onSuccess[] = function (){
            $this->informUser(new UserInformArgs('edit'));
            $this->redirect('default');
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('edit', false, 'errpr', $e));
            $this->redirect('default');
        };
        return $control;
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