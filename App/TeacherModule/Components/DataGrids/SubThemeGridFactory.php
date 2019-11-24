<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 10.4.19
 * Time: 19:24
 */

namespace App\TeacherModule\Components\DataGrids;

use App\CoreModule\Components\DataGrids\BaseGrid;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class SubThemeGridFactory
 * @package App\TeacherModule\Components\DataGrids
 */
class SubThemeGridFactory extends BaseGrid
{
    /**
     * @var SubThemeRepository
     */
    protected $subThemeRepository;

    /**
     * @var ThemeRepository
     */
    protected $themeRepository;

    /**
     * SubThemeGridFactory constructor.
     * @param SubThemeRepository $subThemeRepository
     * @param ThemeRepository $themeRepository
     */
    public function __construct
    (
        SubThemeRepository $subThemeRepository, ThemeRepository $themeRepository
    )
    {
        parent::__construct();
        $this->subThemeRepository = $subThemeRepository;
        $this->themeRepository = $themeRepository;
    }

    /**
     * @param $container
     * @param $name
     * @return DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     * @throws \Exception
     */
    public function create($container, $name): DataGrid
    {
        $grid = parent::create($container, $name);

        $themeOptions = $this->themeRepository->findAssoc([], 'id');
        bdump($themeOptions);

        $themeOptions = $this->themeRepository->findAllowed($container->user);
        bdump($themeOptions);

        $grid->setPrimaryKey('id');

        $grid->setDataSource($this->subThemeRepository->getSecuredQueryBuilder($container->user));

        $grid->addColumnNumber('id', 'ID')
            ->setFitContent()
            ->setSortable()
            ->setFilterText();

        $grid->addColumnDateTime('created', 'Vytvořeno')
            ->addAttributes(['class' => 'text-center'])
            ->setFormat('d.m.Y H:i:s')
            ->setSortable();

        $grid->addColumnText('label', 'Název')
            ->setFilterText();

        $grid->addColumnStatus('theme', 'Téma', 'theme.id')
            ->addAttributes(['class' => 'text-center'])
            ->setOptions($themeOptions)
            ->setSortable('er.theme')
            ->onChange[] = [$container, 'handleThemeUpdate'];

        $grid->addFilterMultiSelect('theme', '', $themeOptions);

        return $grid;
    }
}