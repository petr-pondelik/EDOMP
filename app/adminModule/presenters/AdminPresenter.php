<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:06
 */

namespace App\AdminModule\Presenters;


use App\Presenters\BasePresenter;

/**
 * Class AdminPresenter
 * @package App\AdminModule\Presenters
 */
abstract class AdminPresenter extends BasePresenter
{
    public function startup()
    {
        parent::startup();
    }
}