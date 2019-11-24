<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.4.19
 * Time: 22:18
 */

namespace App\TeacherModule\Presenters;


use App\CoreModule\Arguments\UserInformArgs;
use App\CoreModule\Arguments\ValidatorArgument;
use App\TeacherModule\Components\DataGrids\GroupGridFactory;
use App\TeacherModule\Components\Forms\GroupForm\IGroupFormFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Functionality\GroupFunctionality;
use App\CoreModule\Model\Persistent\Repository\GroupRepository;
use App\CoreModule\Model\Persistent\Repository\SuperGroupRepository;
use App\CoreModule\Services\Authorizator;
use App\TeacherModule\Services\NewtonApiClient;
use App\CoreModule\Services\Validator;
use Nette\Utils\ArrayHash;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class GroupPresenter
 * @package App\TeacherModule\Presenters
 */
class GroupPresenter extends EntityPresenter
{
    /**
     * @var SuperGroupRepository
     */
    protected $superGroupRepository;

    /**
     * GroupPresenter constructor.
     * @param Authorizator $authorizator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param GroupRepository $groupRepository
     * @param GroupFunctionality $groupFunctionality
     * @param SuperGroupRepository $superGroupRepository
     * @param Validator $validator
     * @param GroupGridFactory $groupGridFactory
     * @param IGroupFormFactory $groupFormFactory
     * @param IHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        GroupRepository $groupRepository, GroupFunctionality $groupFunctionality, SuperGroupRepository $superGroupRepository,
        Validator $validator,
        GroupGridFactory $groupGridFactory, IGroupFormFactory $groupFormFactory,
        IHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct(
            $authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory,
            $groupRepository, $groupFunctionality, $groupGridFactory, $groupFormFactory
        );
        $this->superGroupRepository = $superGroupRepository;
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
            $container->setDefaults([ 'label' => $item->getLabel() ]);
        };

        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleInlineUpdate'];

        return $grid;
    }

    /**
     * @param int $groupId
     * @param int $superGroupId
     * @throws \Exception
     */
    public function handleSuperGroupUpdate(int $groupId, int $superGroupId): void
    {
        try{
            $this->functionality->update($groupId, ArrayHash::from([
                'superGroup' => $superGroupId
            ]));
        }
        catch (\Exception $e){
            $this->informUser(new UserInformArgs('superGroup', true, 'error', $e, true));
        }
        $this->informUser(new UserInformArgs('superGroup', true, 'success', null, true));
        $this['entityGrid']->reload();
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