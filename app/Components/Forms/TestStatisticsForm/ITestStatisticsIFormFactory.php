<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.5.19
 * Time: 9:45
 */

namespace App\Components\Forms\TestStatisticsForm;


use App\Components\Forms\IFormFactory;

/**
 * Class ITestStatisticsIFormFactory
 * @package App\Components\Forms\TestStatisticsForm
 */
interface ITestStatisticsIFormFactory extends IFormFactory
{
    /**
     * @return TestStatisticsFormControl
     */
    public function create(): TestStatisticsFormControl;
}