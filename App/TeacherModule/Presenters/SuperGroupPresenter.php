<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.4.19
 * Time: 22:19
 */

namespace App\TeacherModule\Presenters;


use App\CoreModule\Arguments\ValidatorArgument;
use App\TeacherModule\Components\DataGrids\SuperGroupGridFactory;
use App\TeacherModule\Components\Forms\SuperGroupForm\ISuperGroupIFormFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Functionality\SuperGroupFunctionality;
use App\CoreModule\Model\Persistent\Repository\SuperGroupRepository;
use App\CoreModule\Services\Authorizator;
use App\TeacherModule\Services\NewtonApiClient;
use App\CoreModule\Services\Validator;
use Nette\Utils\ArrayHash;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class SuperGroupPresenter
 * @package App\TeacherModule\Presenters
 */
class SuperGroupPresenter extends EntityPresenter
{
    /**
     * SuperGroupPresenter constructor.
     * @param Authorizator $authorizator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param SuperGroupRepository $superGroupRepository
     * @param SuperGroupFunctionality $superGroupFunctionality
     * @param SuperGroupGridFactory $superGroupGridFactory
     * @param ISuperGroupIFormFactory $superGroupFormFactory
     * @param Validator $validator
     * @param IHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        SuperGroupRepository $superGroupRepository, SuperGroupFunctionality $superGroupFunctionality,
        SuperGroupGridFactory $superGroupGridFactory, ISuperGroupIFormFactory $superGroupFormFactory,
        Validator $validator,
        IHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct(
            $authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory,
            $superGroupRepository, $superGroupFunctionality, $superGroupGridFactory, $superGroupFormFactory
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
            ->setClass('btn btn-danger btn-sm ajax');

        $grid->addAction('edit', '', 'update!')
            ->setIcon('edit')
            ->setClass('btn btn-primary btn-sm');

        $grid->addInlineEdit()
            ->setIcon('pencil-alt')
            ->setTitle('Upravit inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = static function ($container) {
            $container->addText('label', '');
        };

        $grid->getInlineEdit()->onSetDefaults[] = static function ($container, $item) {
            $container->setDefaults([
                'label' => $item->getLabel()
            ]);
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
        $validationFields['label'] = new ValidatorArgument($row->label, 'stringNotEmpty');
        return $this->validator->validatePlain($validationFields);
    }
}