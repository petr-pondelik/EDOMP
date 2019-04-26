<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 17.4.19
 * Time: 23:22
 */

namespace App\Components\DataGrids;

use App\Model\Managers\UserManager;

/**
 * Class UserGridFactory
 * @package App\Components\DataGrids
 */
class UserGridFactory extends BaseGrid
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * UserGridFactory constructor.
     * @param UserManager $userManager
     */
    public function __construct
    (
        UserManager $userManager
    )
    {
        parent::__construct();
        $this->userManager = $userManager;
    }

    /**
     * @param $container
     * @param $name
     * @return \Ublaboo\DataGrid\DataGrid
     * @throws \Dibi\NotSupportedException
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function create($container, $name)
    {
        $grid = parent::create($container, $name);

        $grid->setPrimaryKey("user_id");

        $grid->setDataSource($this->userManager->getSelect());

        $grid->addColumnNumber("user_id", "ID")
            ->setSortable();

        $grid->addColumnDateTime("created", "Vytvořeno")
            ->addAttributes(['class' => 'text-center'] )
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText("username", "Uživatelské jméno");

        return $grid;
    }
}