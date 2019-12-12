<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:07
 */

namespace App\TeacherModule\Presenters;

use App\CoreModule\Arguments\ValidatorArgument;
use App\TeacherModule\Components\DataGrids\ThemeGridFactory;
use App\TeacherModule\Components\Forms\ThemeForm\IThemeFormFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Entity\Theme;
use App\CoreModule\Model\Persistent\Functionality\ThemeFunctionality;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use App\CoreModule\Services\Authorizator;
use App\TeacherModule\Services\NewtonApiClient;
use App\CoreModule\Services\Validator;
use Nette\Utils\ArrayHash;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class ThemePresenter
 * @package App\TeacherModule\Presenters
 */
final class ThemePresenter extends EntityPresenter
{
    /**
     * ThemePresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param ThemeRepository $themeRepository
     * @param ThemeFunctionality $themeFunctionality
     * @param ThemeGridFactory $themeGridFactory
     * @param IThemeFormFactory $themeFormFactory
     * @param IHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        ThemeRepository $themeRepository, ThemeFunctionality $themeFunctionality,
        ThemeGridFactory $themeGridFactory, IThemeFormFactory $themeFormFactory,
        IHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct(
            $authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory,
            $themeRepository, $themeFunctionality, $themeGridFactory, $themeFormFactory
        );
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
            ->setClass('btn btn-danger btn-sm ajax')
            ->setTitle('Odstranit');

        $grid->addAction('update', '', 'update!')
            ->setIcon('edit')
            ->setClass('btn btn-primary btn-sm')
            ->setTitle('Editovat');

        $grid->addInlineEdit()
            ->setIcon('pencil-alt')
            ->setTitle('Editovat inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = static function ($container) {
                $container->addText('label', '');
            };

        $grid->getInlineEdit()->onSetDefaults[] = static function ($cont, Theme $item) {
            $cont->setDefaults([ 'label' => $item->getLabel() ]);
        };

        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleInlineUpdate'];

        return $grid;
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