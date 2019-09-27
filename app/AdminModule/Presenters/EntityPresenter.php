<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 17.9.19
 * Time: 21:43
 */

namespace App\AdminModule\Presenters;


use App\Arguments\UserInformArgs;
use App\Components\Forms\EntityFormControl;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\ISideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Functionality\BaseFunctionality;
use App\Model\Persistent\Repository\BaseRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use App\Services\Validator;

/**
 * Class EntityPresenter
 * @package App\AdminModule\Presenters
 */
abstract class EntityPresenter extends AdminPresenter
{
    /**
     * @var BaseRepository
     */
    protected $repository;

    /**
     * @var BaseFunctionality
     */
    protected $functionality;

    protected $gridFactory;

    protected $formFactory;

    /**
     * EntityPresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     * @param BaseRepository $repository
     * @param BaseFunctionality $functionality
     * @param $gridFactory
     * @param $formFactory
     */
    public function __construct
    (
        Authorizator $authorizator,
        Validator $validator,
        NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory,
        ISideBarFactory $sideBarFactory,
        FlashesTranslator $flashesTranslator, ISectionHelpModalFactory $sectionHelpModalFactory,
        BaseRepository $repository,
        BaseFunctionality $functionality,
        $gridFactory,
        $formFactory
    )
    {
        parent::__construct($authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory);
        $this->repository = $repository;
        $this->functionality = $functionality;
        $this->gridFactory = $gridFactory;
        $this->formFactory = $formFactory;
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function actionUpdate(int $id): void
    {
        bdump('ACTION UPDATE');
        $entity = $this->repository->find($id);
        if(!$this->isEntityAllowed($entity)){
            $this->flashMessage('Nedostatečná přístupová práva.', 'danger');
            $this->redirect('default');
        }
        $formControl = $this['entityForm'];
        $formControl->setEntity($entity);
        $this->template->entity = $entity;
        if(!$formControl->isSubmitted()){
            $formControl->setDefaults();
        }
    }

    public function reloadEntity(): void
    {
        bdump('RELOAD ENTITY');
        $id = $this->getParameter('id');
        if($id && $this->getAction() === 'update'){
            $entity = $this->repository->find($id);
            $this->template->entity = $entity;
            $this['entityForm']->setEntity($entity);
            $this['entityForm']->setDefaults();
            $this['entityForm']->redrawControl('entityLabelSnippet');
            $this->redrawControl('breadcrumbSnippet');
        }
    }

    /**
     * @param BaseEntity $entity
     * @return bool
     */
    public function isEntityAllowed(BaseEntity $entity): bool
    {
        return true;
    }

    /**
     * @param int $id
     */
    public function handleDelete(int $id): void
    {
        try{
            $this->functionality->delete($id);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('delete', true,'error', $e, true));
            return;
        }
        $this['entityGrid']->reload();
        $this->informUser(new UserInformArgs('delete', true, 'success', null, true));
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleUpdate(int $id): void
    {
        $this->redirect('update', $id);
    }

    /**
     * @return EntityFormControl
     */
    public function createComponentEntityForm(): EntityFormControl
    {
        bdump($this->getParameters());
        $control = $this->formFactory->create();
        $control->onSuccess[] = function () {
            $this['entityGrid']->reload();
            $this->informUser(new UserInformArgs($this->getAction(), true, 'success', null, false, 'entityForm'));
            $this->reloadEntity();
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs($this->getAction(), true, 'error', $e, false, 'entityForm'));
        };
        return $control;
    }

    public function renderUpdate(): void
    {
        $formControl = $this['entityForm'];
        $formControl->initComponents();
    }
}