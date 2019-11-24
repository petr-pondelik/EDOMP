<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 17.4.19
 * Time: 23:22
 */

namespace App\TeacherModule\Components\DataGrids;

use App\CoreModule\Components\DataGrids\BaseGrid;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Model\Persistent\Repository\RoleRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class UserGridFactory
 * @package App\TeacherModule\Components\DataGrids
 */
class UserGridFactory extends BaseGrid
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * UserGridFactory constructor.
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        UserRepository $userRepository, RoleRepository $roleRepository,
        ConstHelper $constHelper
    )
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->constHelper = $constHelper;
    }

    /**
     * @param $container
     * @param $name
     * @return \Ublaboo\DataGrid\DataGrid
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function create($container, $name): DataGrid
    {
        $grid = parent::create($container, $name);
        $roleOptions = $this->roleRepository->findAllowed($container->user);

        $grid->setPrimaryKey('id');

        $grid->setDataSource($this->userRepository->getSecuredQueryBuilder($container->user));

        $grid->addColumnNumber('id', 'ID')
            ->setFitContent()
            ->setSortable()
            ->setFilterText();

        $grid->addColumnDateTime('created', 'Vytvořeno')
            ->addAttributes(['class' => 'text-center'])
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('email', 'E-mail')
            ->setFilterText();

        $grid->addColumnText('username', 'Uživatelské jméno')
            ->setFilterText();

        $grid->addColumnText('firstName', 'Jméno')
            ->setFilterText();

        $grid->addColumnText('lastName', 'Příjmení')
            ->setFilterText();

        $grid->addColumnNumber('role', 'Role')
            ->setSortable('er.id')
            ->addAttributes(['class' => 'text-center'])
            ->setReplacement($roleOptions)
            ->setFilterMultiSelect($roleOptions);


        return $grid;
    }
}