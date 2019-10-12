<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 9:40
 */

namespace App\AdminModule\Presenters;

use App\Arguments\UserInformArgs;
use App\Components\DataGrids\TemplateGridFactory;
use App\Components\HeaderBar\IHeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\ISideBarFactory;
use App\Helpers\ConstHelper;
use App\Helpers\FlashesTranslator;
use App\Model\Persistent\Functionality\BaseFunctionality;
use App\Model\Persistent\Repository\BaseRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use App\Services\ProblemTemplateSession;
use App\Services\Validator;
use Nette\Utils\ArrayHash;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class ProblemTemplatePresenter
 * @package App\AdminModule\Presenters
 */
abstract class ProblemTemplatePresenter extends EntityPresenter
{
    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var ProblemTemplateSession
     */
    protected $problemTemplateSession;

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var int
     */
    protected $typeId;

    /**
     * ProblemTemplatePresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param TemplateGridFactory $templateGridFactory
     * @param ConstHelper $constHelper
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     * @param ProblemTemplateSession $problemTemplateSession
     * @param BaseRepository $repository
     * @param BaseFunctionality $functionality
     * @param $formFactory
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        TemplateGridFactory $templateGridFactory,
        ConstHelper $constHelper, ISectionHelpModalFactory $sectionHelpModalFactory,
        ProblemTemplateSession $problemTemplateSession,
        BaseRepository $repository, BaseFunctionality $functionality, $formFactory
    )
    {
        parent::__construct(
            $authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory,
            $repository, $functionality, $templateGridFactory, $formFactory
        );
        $this->constHelper = $constHelper;
        $this->problemTemplateSession = $problemTemplateSession;
    }

    public function actionDefault(): void
    {
        bdump('ACTION DEFAULT');
        if ($this->getParameter('do') === null && $this->getParameter('preserveValidation') === null) {
            $this->problemTemplateSession->erase();
        }
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function actionUpdate(int $id): void
    {
        bdump('ACTION UPDATE');

        if (!$entity = $this->safeFind($id)) {
            $this->redirect('default');
        }

        $formControl = $this['entityForm'];
        $formControl->setEntity($entity);
        $this->template->entity = $entity;
        if (!$formControl->isSubmitted()) {
            if ($this->getParameter('do') === null && $this->getParameter('preserveValidation') === null) {
                $this->problemTemplateSession->erase();
            }
            $formControl->setDefaults();
        }
    }

    /**
     * @param $name
     * @return DataGrid
     */
    public function createComponentEntityGrid($name): DataGrid
    {
        $grid = $this->gridFactory->create($this, $name, $this->repository, $this->typeId);

        $grid->addAction('delete', '', 'delete!')
            ->setIcon('trash')
            ->setTitle('Odstranit šablonu')
            ->setClass('btn btn-sm btn-danger ajax');

        $grid->addAction('edit', '', 'update!')
            ->setIcon('edit')
            ->setTitle('Editovat šablonu')
            ->setClass('btn btn-primary btn-sm');

        $grid->addInlineEdit()
            ->setIcon('pencil-alt')
            ->setTitle('Upravit inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = static function ($container) {
            $container->addText('textBefore', '');
            $container->addText('textAfter', '');
        };

        $grid->getInlineEdit()->onSetDefaults[] = static function ($cont, $item) {
            $cont->setDefaults([
                'textBefore' => $item->getTextBefore(),
                'textAfter' => $item->getTextAfter(),
            ]);
        };

        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleInlineUpdate'];

        return $grid;
    }

    /**
     * @param int $id
     * @param ArrayHash $row
     */
    public function handleInlineUpdate(int $id, ArrayHash $row): void
    {
        try{
            $errors = $this->validateInlineUpdate($row);
            if ($errors) {
                $this->informUser(new UserInformArgs('update', true, 'error', null, true, null, $errors[0]));
                return;
            }
            $this->functionality->update($id, $row, true, true);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('update', true,'error', $e, true));
            return;
        }
        $this->informUser(new UserInformArgs('update', true, 'success', null, true));
    }

    /**
     * @param int $subCategoryId
     * @param int $templateId
     */
    public function handleSubCategoryUpdate(int $templateId, int $subCategoryId): void
    {
        try {
            $this->functionality->update($templateId, ArrayHash::from(['subCategory' => $subCategoryId]), true, true);
        } catch (\Exception $e) {
            $this->informUser(new UserInformArgs('subCategory', true, 'error', $e, true));
        }
        $this['entityGrid']->reload();
        $this->informUser(new UserInformArgs('subCategory', true, 'success', null, true));
    }

    /**
     * @param int $templateId
     * @param int $difficultyId
     */
    public function handleDifficultyUpdate(int $templateId, int $difficultyId): void
    {
        try {
            $this->functionality->update($templateId, ArrayHash::from(['difficulty' => $difficultyId]), true, true);
        } catch (\Exception $e) {
            $this->informUser(new UserInformArgs('difficulty', true, 'error', $e, true));
            return;
        }
        $this['entityGrid']->reload();
        $this->informUser(new UserInformArgs('difficulty', true, 'success', null, true));
    }

    /**
     * @param array $data
     */
    public function handleTypeValidation(array $data): void
    {
        $this['entityForm']->handleTypeValidation($data);
    }

    /**
     * @param array $data
     */
    public function handleCondValidation(array $data): void
    {
        $this['entityForm']->handleCondValidation($data);
    }
}