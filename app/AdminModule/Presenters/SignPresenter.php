<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 17:24
 */

namespace App\AdminModule\Presenters;


use App\Presenters\BaseSignPresenter;

/**
 * Class SignPresenter
 * @package App\AdminModule\Presenters
 */
class SignPresenter extends BaseSignPresenter
{
    /**
     * @var bool
     */
    protected $admin = true;
}