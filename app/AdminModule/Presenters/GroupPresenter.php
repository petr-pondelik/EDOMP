<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.4.19
 * Time: 22:18
 */

namespace App\AdminModule\Presenters;


use App\Arguments\UserInformArgs;
use App\Components\DataGrids\GroupGridFactory;
use App\Components\Forms\GroupForm\IGroupIFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\ISideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Functionality\GroupFunctionality;
use App\Model\Persistent\Repository\GroupRepository;
use App\Model\Persistent\Repository\SuperGroupRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use App\Services\Validator;
use Nette\Utils\ArrayHash;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class GroupPresenter
 * @package App\AdminModule\Presenters
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
     * @param HeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param GroupRepository $groupRepository
     * @param GroupFunctionality $groupFunctionality
     * @param SuperGroupRepository $superGroupRepository
     * @param Validator $validator
     * @param GroupGridFactory $groupGridFactory
     * @param IGroupIFormFactory $groupFormFactory
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        GroupRepository $groupRepository, GroupFunctionality $groupFunctionality, SuperGroupRepository $superGroupRepository,
        Validator $validator,
        GroupGridFactory $groupGridFactory, IGroupIFormFactory $groupFormFactory,
        ISectionHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct(
            $authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory,
            $groupRepository, $groupFunctionality, $groupGridFactory, $groupFormFactory
        );
        $this->superGroupRepository = $superGroupRepository;
    }

    /**
     * @param BaseEntity $entity
     * @return bool
     */
    public function isEntityAllowed(BaseEntity $entity): bool
    {
        return $this->user->isInRole('admin') || $this->authorizator->isEntityAllowed($this->user->identity, $entity);
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
     * @param int $id
     * @param $row
     * @throws \Exception
     */
    public function handleInlineUpdate(int $id, $row): void
    {
        try{
            $this->functionality->update($id, $row);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('update', true,'error', $e, true));
        }
        $this->informUser(new UserInformArgs('update', true, 'success', null, true));
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
}