<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 13.4.19
 * Time: 18:05
 */

namespace App\TeacherModule\Presenters;

use App\CoreModule\Arguments\UserInformArgs;
use App\CoreModule\Arguments\ValidatorArgument;
use App\Components\DataGrids\LogoGridFactory;
use App\CoreModule\Components\Forms\EntityFormControl;
use App\Components\Forms\LogoForm\ILogoIFormFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\Model\Persistent\Functionality\LogoFunctionality;
use App\Model\Persistent\Repository\LogoRepository;
use App\Services\Authorizator;
use App\Services\FileService;
use App\TeacherModule\Services\NewtonApiClient;
use App\Services\Validator;
use Nette\Application\Responses\TextResponse;
use Nette\IOException;
use Nette\Utils\ArrayHash;

/**
 * Class LogoPresenter
 * @package App\Presenters
 */
class LogoPresenter extends EntityPresenter
{
    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * LogoPresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param LogoRepository $logoRepository
     * @param LogoFunctionality $logoFunctionality
     * @param FileService $fileService
     * @param LogoGridFactory $logoGridFactory
     * @param ILogoIFormFactory $logoFormFactory
     * @param IHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        LogoRepository $logoRepository, LogoFunctionality $logoFunctionality,
        FileService $fileService,
        LogoGridFactory $logoGridFactory, ILogoIFormFactory $logoFormFactory,
        IHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct
        (
            $authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory,
            $logoRepository, $logoFunctionality, $logoGridFactory, $logoFormFactory
        );
        $this->fileService = $fileService;
    }

    public function renderDefault():void
    {
        $this->functionality->deleteEmpty();
        $this->fileService->clearLogosTmpDir();
    }

    /**
     * @param $name
     */
    public function createComponentEntityGrid($name): void
    {
        $grid = $this->gridFactory->create($this, $name);

        $grid->addAction('delete', '', 'delete!')
            ->setTemplate(__DIR__ . '/templates/Logo/delete_action.latte');

        $grid->setItemsDetail(__DIR__ . '/templates/Logo/detail.latte')
            ->setClass('btn btn-sm btn-primary ajax');

        $grid->addAction('edit', '', 'update!')
            ->setIcon('edit')
            ->setClass('btn btn-primary btn-sm');

        $grid->addInlineEdit()
            ->setIcon('pencil-alt')
            ->setTitle('Upravit inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = static function ($container) {
            $container->addText('label', '');
        };

        $grid->getInlineEdit()->onSetDefaults[] = static function ($container, $item) {
            $container->setDefaults([
                'label' => $item->getLabel()
            ]);
        };

        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleInlineUpdate'];

    }

//    /**
//     * @param int $id
//     * @param $row
//     */
//    public function handleInlineUpdate(int $id, $row): void
//    {
//        try{
//            $this->functionality->update($id, $row);
//        } catch (\Exception $e){
//            $this->informUser(new UserInformArgs('update', true,'error', $e, true));
//        }
//        $this->informUser(new UserInformArgs('update', true, 'success',null, true));
//    }

    /**
     * @throws \Nette\Application\AbortException
     * @throws \Exception
     */
    public function handleUploadFile(): void
    {
        $this->sendResponse( new TextResponse($this->fileService->uploadFile($this->getHttpRequest())) );
    }

    /**
     * @throws \Nette\Application\AbortException
     * @throws \Exception
     */
    public function handleRevertFileUpload(): void
    {
        $this->sendResponse( new TextResponse($this->fileService->revertFileUpload($this->getHttpRequest())) );
    }

    /**
     * @throws \Nette\Application\AbortException
     * @throws \Exception
     */
    public function handleUpdateFile(): void
    {
        $this->sendResponse( new TextResponse($this->fileService->updateFile($this->getHttpRequest())) );
    }

    /**
     * @throws \Nette\Application\AbortException
     */
    public function handleRevertFileUpdate(): void
    {
        $this->sendResponse( new TextResponse($this->fileService->revertFileUpdate($this->getHttpRequest())) );
    }

    /**
     * @param ArrayHash $row
     * @return array
     * @throws \App\CoreModule\Exceptions\ValidatorException
     */
    public function validateInlineUpdate(ArrayHash $row): array
    {
        $validationFields['label'] = new ValidatorArgument($row->label, 'stringNotEmpty');
        return $this->validator->validatePlain($validationFields);
    }

    /**
     * @return EntityFormControl
     */
    public function createComponentEntityForm(): EntityFormControl
    {
        $control = $this->formFactory->create();
        $control->onError[] = function ($e) use ($control) {
            if($e instanceof IOException){
                $control->flashMessage('Opakujte prosím volbu souboru loga.', 'danger');
                return;
            }
            $this->informUser(new UserInformArgs($this->getAction(), true, 'error', $e, false, 'entityForm'));
        };
        $control->onSuccess[] = function () use ($control) {
            $this->informUser(new UserInformArgs($this->getAction(), true, 'success', null, false, 'entityForm'));
            $this->reloadEntity();
            if(!$control->isUpdate()){
                $this['entityGrid']->reload();
            }
            if($control->isUpdate()){
                $control->redrawControl();
            }
        };
        return $control;
    }
}