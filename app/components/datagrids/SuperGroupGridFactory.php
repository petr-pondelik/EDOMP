<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 11.4.19
 * Time: 18:43
 */

namespace App\Components\DataGrids;

use App\Model\Managers\SuperGroupManager;

/**
 * Class SuperGroupGridFactory
 * @package App\Components\DataGrids
 */
class SuperGroupGridFactory extends BaseGrid
{
    /**
     * @var SuperGroupManager
     */
    protected $superGroupManager;

    public function __construct
    (
        SuperGroupManager $superGroupManager
    )
    {
        parent::__construct();
        $this->superGroupManager = $superGroupManager;
    }

    public function create($container, $name)
    {
        $grid = parent::create($container, $name);

        $grid->setPrimaryKey("super_group_id");

        $grid->setDataSource($this->superGroupManager->getSelect());

        $grid->addColumnNumber('super_group_id', 'ID')
            ->setSortable();

        $grid->addColumnDateTime('created', 'Vytvořeno')
            ->addAttributes(['class' => 'text-center'] )
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('label', 'Název');

        return $grid;
    }
}