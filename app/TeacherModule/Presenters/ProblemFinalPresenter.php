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
use App\CoreModule\Model\Persistent\Functionality\ProblemFinal\ProblemFinalFunctionality;
use App\CoreModule\Model\Persistent\Repository\ProblemFinal\ProblemFinalRepository;
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
            ->onControlAdd[] = static function($container) {
                $container->addText('textBefore', '');
                $container->addText('textAfter', '');
                $container->addText('result', '');
            };

        $grid->getInlineEdit()->onSetDefaults[] = static function($cont, $item) {
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
     * @param int $subCategoryId
     * @throws \Exception
     */
    public function handleSubCategoryUpdate(int $problemId, int $subCategoryId): void
    {
        try{
            $this->functionality->update($problemId, ArrayHash::from([ 'subCategory' => $subCategoryId ]),true, false);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('subCategory', true, 'error', $e));
            return;
        }
        $this['entityGrid']->reload();
        $this->informUser(new UserInformArgs('subCategory', true));
    }

    /**
     * @param int $problemId
     * @param int $difficultyId
     */
    public function handleDifficultyUpdate(int $problemId, int $difficultyId): void
    {
        try{
            $this->functionality->update($problemId, ArrayHash::from([ 'difficulty' => $difficultyId ]), true, false);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('difficulty', true, 'error', $e));
            return;
        }
        $this['entityGrid']->reload();
        $this->informUser(new UserInformArgs('difficulty', true));
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function handleGetResult(int $id): void
    {
        $problem = $this->repository->find($id);
        $result = null;
        try{
            $this->pluginContainer->getPlugin($problem->getProblemType()->getKeyLabel())->evaluate($problem);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('getRes', true, 'error', $e));
            return;
        }
        $this['entityGrid']->reload();
        $this->informUser(new UserInformArgs('getRes', true));
    }
}