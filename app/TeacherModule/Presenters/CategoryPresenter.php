<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:07
 */

namespace App\TeacherModule\Presenters;

use App\CoreModule\Arguments\ValidatorArgument;
use App\TeacherModule\Components\DataGrids\CategoryGridFactory;
use App\TeacherModule\Components\Forms\CategoryForm\ICategoryFormFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Entity\Category;
use App\CoreModule\Model\Persistent\Functionality\CategoryFunctionality;
use App\CoreModule\Model\Persistent\Repository\CategoryRepository;
use App\CoreModule\Services\Authorizator;
use App\TeacherModule\Services\NewtonApiClient;
use App\CoreModule\Services\Validator;
use Nette\Utils\ArrayHash;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class CategoryPresenter
 * @package App\TeacherModule\Presenters
 */
class CategoryPresenter extends EntityPresenter
{
    /**
     * CategoryPresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param CategoryRepository $categoryRepository
     * @param CategoryFunctionality $categoryFunctionality
     * @param CategoryGridFactory $categoryGridFactory
     * @param ICategoryFormFactory $categoryFormFactory
     * @param IHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        CategoryRepository $categoryRepository, CategoryFunctionality $categoryFunctionality,
        CategoryGridFactory $categoryGridFactory, ICategoryFormFactory $categoryFormFactory,
        IHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct(
            $authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory,
            $categoryRepository, $categoryFunctionality, $categoryGridFactory, $categoryFormFactory
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
            ->setTitle('Odstranit kategorii.');

        $grid->addAction('update', '', 'update!')
            ->setIcon('edit')
            ->setClass('btn btn-primary btn-sm')
            ->setTitle('Editovat kategorii.');

        $grid->addInlineEdit()
            ->setIcon('pencil-alt')
            ->setTitle('Upravit inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = static function ($container) {
                $container->addText('label', '');
            };

        $grid->getInlineEdit()->onSetDefaults[] = static function ($cont, Category $item) {
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