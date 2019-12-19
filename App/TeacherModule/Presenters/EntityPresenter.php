<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 17.9.19
 * Time: 21:43
 */

namespace App\TeacherModule\Presenters;


use App\CoreModule\Arguments\UserInformArgs;
use App\CoreModule\Arguments\ValidatorArgument;
use App\CoreModule\Components\Forms\EntityFormControl;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Functionality\BaseFunctionality;
use App\CoreModule\Model\Persistent\Repository\BaseRepository;
use App\CoreModule\Services\Authorizator;
use App\TeacherModule\Services\NewtonApiClient;
use App\CoreModule\Services\Validator;
use Nette\Utils\ArrayHash;

/**
 * Class EntityPresenter
 * @package App\TeacherModule\Presenters
 */
abstract class EntityPresenter extends TeacherPresenter
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
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param IHelpModalFactory $sectionHelpModalFactory
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
        IHeaderBarFactory $headerBarFactory,
        ISideBarFactory $sideBarFactory,
        FlashesTranslator $flashesTranslator, IHelpModalFactory $sectionHelpModalFactory,
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
     * @return EntityFormControl
     */
    public function getEntityForm(): EntityFormControl
    {
        return $this['entityForm'];
    }

    public function actionCreate(): void
    {
        $this->getEntityForm()->initComponents();
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function actionUpdate(int $id): void
    {
        bdump('ACTION UPDATE');

        if (!$entity = $this->safeFind($id)) {
            $this->redirect('default');
        }

        if (!$this->isEntityAllowed($entity)) {
            $this->flashMessage('Nedostatečná přístupová práva.', 'danger');
            $this->redirect('default');
        }
        $formControl = $this['entityForm'];
        $formControl->setEntity($entity);
        $this->getEntityForm()->initComponents();
        $this->template->entity = $entity;
        if (!$formControl->isSubmitted()) {
            $formControl->setDefaults();
        }
    }

    public function reloadEntity(): void
    {
        bdump('RELOAD ENTITY');
        $id = $this->getParameter('id');
        if ($id && $this->getAction() === 'update') {
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
        return $this->authorizator->isEntityAllowed($this->user, $entity);
    }

    /**
     * @param int $id
     * @throws \App\CoreModule\Exceptions\FlashesTranslatorException
     */
    public function handleDelete(int $id): void
    {
        try {
            $this->functionality->delete($id);
        } catch (\Exception $e) {
            $this->informUser(new UserInformArgs('delete', true, 'error', $e, 'flashesModal'));
            return;
        }
        $this['entityGrid']->reload();
        $this->informUser(new UserInformArgs('delete', true, 'success', null, 'flashesModal'));
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
     * @param int $id
     * @param ArrayHash $row
     * @throws \Exception
     */
    public function handleInlineUpdate(int $id, ArrayHash $row): void
    {
        try {
            $errors = $this->validateInlineUpdate($row);
            if ($errors) {
                $this->informUser(new UserInformArgs('update', true, 'error', null, 'flashesModal', $errors[0]));
                return;
            }
            $this->functionality->update($id, $row);
        } catch (\Exception $e) {
            $this->informUser(new UserInformArgs('update', true, 'error', $e, 'flashesModal'));
            return;
        }
        $this->informUser(new UserInformArgs('update', true, 'success', null, 'flashesModal'));
    }

    /**
     * @param ArrayHash $row
     * @return ValidatorArgument[]
     */
    public function validateInlineUpdate(ArrayHash $row): array
    {
        return [];
    }

    /**
     * @return EntityFormControl
     */
    public function createComponentEntityForm(): EntityFormControl
    {
        $component = $this->getAction() === 'default' ? 'entityForm' : 'flashesModal';
        $control = $this->formFactory->create();
        $control->onSuccess[] = function () use ($component) {
            $this['entityGrid']->reload();
            $this->informUser(new UserInformArgs($this->getAction(), true, 'success', null, $component));
            $this->reloadEntity();
        };
        $control->onError[] = function ($e) use ($component) {
            $this->informUser(new UserInformArgs($this->getAction(), true, 'error', $e, $component));
        };
        return $control;
    }

    public function renderCreate(): void
    {
    }

    public function renderUpdate(): void
    {
        $this->getEntityForm()->fillComponents();
    }

    /**
     * @param int $id
     * @param string $msg
     * @return BaseEntity|null
     */
    public function safeFind(int $id, string $msg = 'Entita nenalezena.'): ?BaseEntity
    {
        $entity = $this->repository->find($id);

        if (!$entity) {
            $this->flashMessage($msg, 'danger');
            $this->redrawControl('flashesSnippet');
            return null;
        }

        return $entity;
    }
}