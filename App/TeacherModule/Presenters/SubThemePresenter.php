<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 17:19
 */

namespace App\TeacherModule\Presenters;

use App\CoreModule\Arguments\UserInformArgs;
use App\CoreModule\Arguments\ValidatorArgument;
use App\TeacherModule\Components\DataGrids\SubThemeGridFactory;
use App\TeacherModule\Components\Forms\SubThemeForm\ISubThemeFormFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Functionality\SubThemeFunctionality;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use App\CoreModule\Services\Authorizator;
use App\TeacherModule\Services\NewtonApiClient;
use App\CoreModule\Services\Validator;
use Nette\Utils\ArrayHash;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class SubThemePresenter
 * @package App\TeacherModule\Presenters
 */
final class SubThemePresenter extends EntityPresenter
{
    /**
     * @var ThemeRepository
     */
    protected $themeRepository;

    /**
     * SubThemePresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param SubThemeRepository $subThemeRepository
     * @param SubThemeFunctionality $subThemeFunctionality
     * @param ThemeRepository $themeRepository
     * @param SubThemeGridFactory $subThemeGridFactory
     * @param ISubThemeFormFactory $subThemeFormFactory
     * @param IHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        SubThemeRepository $subThemeRepository, SubThemeFunctionality $subThemeFunctionality,
        ThemeRepository $themeRepository,
        SubThemeGridFactory $subThemeGridFactory, ISubThemeFormFactory $subThemeFormFactory,
        IHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct(
            $authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory,
            $subThemeRepository, $subThemeFunctionality, $subThemeGridFactory, $subThemeFormFactory
        );
        $this->themeRepository = $themeRepository;
    }

    /**
     * @param $name
     * @return DataGrid
     */
    public function createComponentEntityGrid($name): DataGrid
    {
        $grid = $this->gridFactory->create($this, $name);

        $grid->addAction('delete', '', 'delete!')
            ->setIcon('trash')
            ->setTitle('Odstranit')
            ->setClass('btn btn-danger btn-sm ajax');

        $grid->addAction('edit', '', 'update!')
            ->setIcon('edit')
            ->setTitle('Editovat')
            ->setClass('btn btn-primary btn-sm');

        $grid->addInlineEdit()
            ->setIcon('pencil-alt')
            ->setTitle('Editovat inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = function ($container) {
            $container->addText('label', '');
        };

        $grid->getInlineEdit()->onSetDefaults[] = function ($cont, $item) {
            $cont->setDefaults(['label' => $item->getLabel()]);
        };

        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleInlineUpdate'];

        return $grid;
    }

    /**
     * @param int $subThemeId
     * @param $themeId
     * @throws \App\CoreModule\Exceptions\FlashesTranslatorException
     */
    public function handleThemeUpdate(int $subThemeId, $themeId): void
    {
        bdump('HANDLE THEME UPDATE');
        bdump($themeId);
        try {
            $this->functionality->update($subThemeId,
                ArrayHash::from(['theme' => $themeId])
            );
        } catch (\Exception $e) {
            $this->informUser(new UserInformArgs('theme', true, 'error', $e));
        }
        $this['entityGrid']->reload();
        $this->informUser(new UserInformArgs('theme', true));
    }

    /**
     * @param ArrayHash $row
     * @return array
     * @throws \App\CoreModule\Exceptions\ValidatorException
     */
    public function validateInlineUpdate(ArrayHash $row): array
    {
        $validationFields['label'] = new ValidatorArgument($row->label, 'notEmpty');
        return $this->validator->validatePlain($validationFields);
    }
}