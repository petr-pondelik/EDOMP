<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 17:18
 */

namespace App\TeacherModule\Presenters;

use App\CoreModule\Arguments\UserInformArgs;
use App\TeacherModule\Components\DataGrids\ProblemGridFactory;
use App\TeacherModule\Components\Forms\ProblemFinalForm\IProblemFinalFormFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Functionality\ProblemFinalFunctionality;
use App\CoreModule\Model\Persistent\Repository\ProblemFinalRepository;
use App\CoreModule\Services\Authorizator;
use App\TeacherModule\Services\PluginContainer;
use App\TeacherModule\Services\NewtonApiClient;
use App\CoreModule\Services\Validator;
use Nette\Utils\ArrayHash;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class ProblemFinalPresenter
 * @package App\TeacherModule\Presenters
 */
class ProblemFinalPresenter extends EntityPresenter
{
    /**
     * @var PluginContainer
     */
    protected $pluginContainer;

    /**
     * ProblemFinalPresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param ProblemGridFactory $problemGridFactory
     * @param IProblemFinalFormFactory $problemFinalFormFactory
     * @param ProblemFinalRepository $problemFinalRepository
     * @param ProblemFinalFunctionality $problemFinalFunctionality
     * @param PluginContainer $pluginContainer
     * @param IHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        ProblemGridFactory $problemGridFactory, IProblemFinalFormFactory $problemFinalFormFactory,
        ProblemFinalRepository $problemFinalRepository, ProblemFinalFunctionality $problemFinalFunctionality,
        PluginContainer $pluginContainer,
        IHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct(
            $authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory,
            $problemFinalRepository, $problemFinalFunctionality, $problemGridFactory, $problemFinalFormFactory
        );
        $this->pluginContainer = $pluginContainer;
    }

    /**
     * @param $name
     * @return DataGrid
     */
    public function createComponentEntityGrid($name): DataGrid
    {
        $grid = $this->gridFactory->create($this, $name);

        $grid->addAction('delete', '', 'delete!')
            ->setTemplate(__DIR__ . '/templates/ProblemFinal/removeBtn.latte');

        $grid->addAction('getResult', 'Získat výsledek')
            ->setTitle('Získat výsledek')
            ->setTemplate(__DIR__ . '/templates/ProblemFinal/getResultBtn.latte');

        $grid->addAction('edit', '', 'update!')
            ->setIcon('edit')
            ->setTitle('Editovat')
            ->setClass('btn btn-primary btn-sm');

        $grid->addInlineEdit()
            ->setIcon('pencil-alt')
            ->setTitle('Upravit inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = static function ($container) {
            $container->addText('textBefore', '');
            $container->addText('textAfter', '');
            $container->addText('result', '');
        };

        $grid->getInlineEdit()->onSetDefaults[] = static function ($cont, $item) {
            bdump($cont);
            $cont->setDefaults([
                'textBefore' => $item->getTextBefore(),
                'textAfter' => $item->getTextAfter(),
                'result' => $item->getResult()
            ]);
        };

        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleInlineUpdate'];

        return $grid;
    }

    /**
     * @param int $problemId
     * @param int $subThemeId
     * @throws \Exception
     */
    public function handleSubThemeUpdate(int $problemId, int $subThemeId): void
    {
        try {
            $this->functionality->update($problemId, ArrayHash::from(['subTheme' => $subThemeId]));
        } catch (\Exception $e) {
            $this->informUser(new UserInformArgs('subTheme', true, 'error', $e));
            return;
        }
        $this['entityGrid']->reload();
        $this->informUser(new UserInformArgs('subTheme', true));
    }

    /**
     * @param int $problemId
     * @param int $difficultyId
     * @throws \App\CoreModule\Exceptions\FlashesTranslatorException
     */
    public function handleDifficultyUpdate(int $problemId, int $difficultyId): void
    {
        try {
            $this->functionality->update($problemId, ArrayHash::from(['difficulty' => $difficultyId]));
        } catch (\Exception $e) {
            $this->informUser(new UserInformArgs('difficulty', true, 'error', $e));
            return;
        }
        $this['entityGrid']->reload();
        $this->informUser(new UserInformArgs('difficulty', true));
    }

    /**
     * @param int $problemId
     * @param bool $visible
     * @throws \App\CoreModule\Exceptions\FlashesTranslatorException
     */
    public function handleStudentVisibleUpdate(int $problemId, bool $visible): void
    {
        try {
            $this->functionality->update($problemId, ArrayHash::from(['studentVisible' => $visible]));
        } catch (\Exception $e) {
            $this->informUser(new UserInformArgs('studentVisible', true, 'error', $e));
            return;
        }
        $this['entityGrid']->reload();
        $this->informUser(new UserInformArgs('studentVisible', true));
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function handleGetResult(int $id): void
    {
        $problem = $this->repository->find($id);
        $result = null;
        try {
            $this->pluginContainer->getPlugin($problem->getProblemType()->getKeyLabel())->evaluate($problem);
        } catch (\Exception $e) {
            $this->informUser(new UserInformArgs('getRes', true, 'error', $e));
            return;
        }
        $this['entityGrid']->reload();
        $this->informUser(new UserInformArgs('getRes', true));
    }
}