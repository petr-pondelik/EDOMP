<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 9:40
 */

namespace App\TeacherModule\Presenters;

use App\CoreModule\Arguments\UserInformArgs;
use App\TeacherModule\Components\DataGrids\TemplateGridFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Functionality\BaseFunctionality;
use App\CoreModule\Model\Persistent\Repository\BaseRepository;
use App\CoreModule\Services\Authorizator;
use App\TeacherModule\Services\NewtonApiClient;
use App\TeacherModule\Services\ProblemTemplateSession;
use App\CoreModule\Services\Validator;
use Nette\Utils\ArrayHash;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class ProblemTemplatePresenter
 * @package App\TeacherModule\Presenters
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
     * @param IHelpModalFactory $sectionHelpModalFactory
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
        ConstHelper $constHelper, IHelpModalFactory $sectionHelpModalFactory,
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
        bdump($this->problemTemplateSession->getProblemTemplate());
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
            ->setTitle('Odstranit')
            ->setClass('btn btn-sm btn-danger ajax');

        $grid->addAction('edit', '', 'update!')
            ->setIcon('edit')
            ->setTitle('Editovat')
            ->setClass('btn btn-primary btn-sm');

        $grid->addInlineEdit()
            ->setIcon('pencil-alt')
            ->setTitle('Editovat inline')
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
     * @throws \App\CoreModule\Exceptions\FlashesTranslatorException
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
     * @param int $subThemeId
     * @param int $templateId
     * @throws \App\CoreModule\Exceptions\FlashesTranslatorException
     */
    public function handleSubThemeUpdate(int $templateId, int $subThemeId): void
    {
        try {
            $this->functionality->update($templateId, ArrayHash::from(['subTheme' => $subThemeId]), true, true);
        } catch (\Exception $e) {
            $this->informUser(new UserInformArgs('subTheme', true, 'error', $e, true));
        }
        $this['entityGrid']->reload();
        $this->informUser(new UserInformArgs('subTheme', true, 'success', null, true));
    }

    /**
     * @param int $templateId
     * @param int $difficultyId
     * @throws \App\CoreModule\Exceptions\FlashesTranslatorException
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
     * @param int $problemId
     * @param bool $visible
     * @throws \App\CoreModule\Exceptions\FlashesTranslatorException
     */
    public function handleStudentVisibleUpdate(int $problemId, bool $visible): void
    {
        try {
            $this->functionality->update($problemId, ArrayHash::from(['studentVisible' => $visible]), true, true);
        } catch (\Exception $e) {
            $this->informUser(new UserInformArgs('studentVisible', true, 'error', $e));
            return;
        }
        $this['entityGrid']->reload();
        $this->informUser(new UserInformArgs('studentVisible', true));
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