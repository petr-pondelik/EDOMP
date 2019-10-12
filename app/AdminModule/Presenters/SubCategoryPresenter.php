<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 17:19
 */

namespace App\AdminModule\Presenters;

use App\Arguments\UserInformArgs;
use App\Arguments\ValidatorArgument;
use App\Components\DataGrids\SubCategoryGridFactory;
use App\Components\Forms\SubCategoryForm\ISubCategoryFormFactory;
use App\Components\HeaderBar\IHeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\ISideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Model\Persistent\Functionality\SubCategoryFunctionality;
use App\Model\Persistent\Repository\CategoryRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use App\Services\Validator;
use Nette\Utils\ArrayHash;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class SubCategoryPresenter
 * @package App\AdminModule\Presenters
 */
class SubCategoryPresenter extends EntityPresenter
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * SubCategoryPresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param SubCategoryRepository $subCategoryRepository
     * @param SubCategoryFunctionality $subCategoryFunctionality
     * @param CategoryRepository $categoryRepository
     * @param SubCategoryGridFactory $subCategoryGridFactory
     * @param ISubCategoryFormFactory $subCategoryFormFactory
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        SubCategoryRepository $subCategoryRepository, SubCategoryFunctionality $subCategoryFunctionality,
        CategoryRepository $categoryRepository,
        SubCategoryGridFactory $subCategoryGridFactory, ISubCategoryFormFactory $subCategoryFormFactory,
        ISectionHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct(
            $authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory,
            $subCategoryRepository, $subCategoryFunctionality, $subCategoryGridFactory, $subCategoryFormFactory
        );
        $this->categoryRepository = $categoryRepository;
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
            ->setClass('btn btn-danger btn-sm ajax');

        $grid->addAction('edit', '', 'update!')
            ->setIcon('edit')
            ->setClass('btn btn-primary btn-sm');

        $grid->addInlineEdit()
            ->setIcon('pencil-alt')
            ->setTitle('Upravit inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = function($container) {
            $container->addText('label', '');
        };

        $grid->getInlineEdit()->onSetDefaults[] = function($cont, $item) {
            $cont->setDefaults([ 'label' => $item->getLabel() ]);
        };

        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleInlineUpdate'];

        return $grid;
    }

    /**
     * @param int $subCategoryId
     * @param $categoryId
     */
    public function handleCategoryUpdate(int $subCategoryId, $categoryId): void
    {
        try{
            $this->functionality->update($subCategoryId,
                ArrayHash::from(['category' => $categoryId])
            );
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('category', true,'error', $e));
        }
        $this['entityGrid']->reload();
        $this->informUser(new UserInformArgs('category', true));
    }

    /**
     * @param ArrayHash $row
     * @return array
     * @throws \App\Exceptions\ValidatorException
     */
    public function validateInlineUpdate(ArrayHash $row): array
    {
        $validationFields['label'] = new ValidatorArgument($row->label, 'notEmpty');
        return $this->validator->validatePlain($validationFields);
    }
}