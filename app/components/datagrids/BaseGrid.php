<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.2.19
 * Time: 23:54
 */

namespace App\Components\DataGrids;

use Nette\Application\UI\Control;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Localization\SimpleTranslator;

/**
 * Class BaseGrid
 * @package app\components\datagrids
 */
class BaseGrid extends Control
{

    /**
     * @param $container
     * @param $name
     * @return DataGrid
     */
    public function create($container, $name)
    {
        $grid = new DataGrid($container, $name);

        $translator = new SimpleTranslator([
            'ublaboo_datagrid.no_item_found_reset' => 'Žádné položky nenalezeny. Filtr můžete vynulovat',
            'ublaboo_datagrid.no_item_found' => 'Žádné položky nenalezeny.',
            'ublaboo_datagrid.here' => 'zde',
            'ublaboo_datagrid.items' => 'Položky',
            'ublaboo_datagrid.all' => 'všechny',
            'ublaboo_datagrid.from' => 'z',
            'ublaboo_datagrid.reset_filter' => 'Resetovat filtr',
            'ublaboo_datagrid.group_actions' => 'Hromadné akce',
            'ublaboo_datagrid.show_all_columns' => 'Zobrazit všechny sloupce',
            'ublaboo_datagrid.hide_column' => 'Skrýt sloupec',
            'ublaboo_datagrid.action' => 'Akce',
            'ublaboo_datagrid.previous' => 'Předchozí',
            'ublaboo_datagrid.next' => 'Další',
            'ublaboo_datagrid.choose' => 'Vyberte',
            'ublaboo_datagrid.execute' => 'Provést',
            'ublaboo_datagrid.cancel' => 'Zavřít',
            'ublaboo_datagrid.save' => 'Uložit',
            'ublaboo_datagrid.multiselect_selected' => 'Více položek',
            'ublaboo_datagrid.multiselect_choose' => 'Vyberte',
            'ublaboo_datagrid.show' => 'Detail',

            'Name' => 'Jméno',
            'Inserted' => 'Vloženo'
        ]);

        $grid->setTranslator($translator);

        $grid->setTemplateFile(APP_DIR . '/presenters/templates/UblabooDatagrid/datagridCustom.latte');

        return $grid;
    }

}