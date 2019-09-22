<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 17.4.19
 * Time: 22:00
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\UserGridFactory;
use App\Components\Forms\UserForm\IUserIFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\ISideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Functionality\UserFunctionality;
use App\Model\Persistent\Repository\UserRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use App\Services\Validator;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class UserPresenter
 * @package App\AdminModule\Presenters
 */
class UserPresenter extends EntityPresenter
{
    /**
     * UserPresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param UserRepository $userRepository
     * @param UserFunctionality $userFunctionality
     * @param UserGridFactory $userGridFactory
     * @param IUserIFormFactory $userFormFactory
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        UserRepository $userRepository, UserFunctionality $userFunctionality,
        UserGridFactory $userGridFactory, IUserIFormFactory $userFormFactory,
        ISectionHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct(
            $authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory,
            $userRepository, $userFunctionality, $userGridFactory, $userFormFactory
        );
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
            ->setTitle('Odstranit uživatele')
            ->setClass('btn btn-danger btn-sm ajax');
        $grid->addAction('edit', '', 'update!')
            ->setIcon('edit')
            ->setTitle('Editovat uživatele')
            ->setClass('btn btn-primary btn-sm');
        return $grid;
    }
}