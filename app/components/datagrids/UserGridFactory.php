<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 17.4.19
 * Time: 23:22
 */

namespace App\Components\DataGrids;

use App\Model\Repository\UserRepository;

/**
 * Class UserGridFactory
 * @package App\Components\DataGrids
 */
class UserGridFactory extends BaseGrid
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * UserGridFactory constructor.
     * @param UserRepository $userRepository
     */
    public function __construct
    (
        UserRepository $userRepository
    )
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    /**
     * @param $container
     * @param $name
     * @return \Ublaboo\DataGrid\DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function create($container, $name)
    {
        $grid = parent::create($container, $name);

        $grid->setPrimaryKey("id");

        $grid->setDataSource($this->userRepository->createQueryBuilder("er"));

        $grid->addColumnNumber("id", "ID")
            ->setSortable();

        $grid->addColumnDateTime("created", "Vytvořeno")
            ->addAttributes(['class' => 'text-center'] )
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText("username", "Uživatelské jméno");

        return $grid;
    }
}