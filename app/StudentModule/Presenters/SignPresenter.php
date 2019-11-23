<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 20.3.19
 * Time: 20:25
 */

namespace App\StudentModule\Presenters;


use App\CoreModule\Presenters\BaseSignPresenter;

/**
 * Class SignPresenter
 * @package App\StudentModule\Presenters
 */
final class SignPresenter extends BaseSignPresenter
{
    /**
     * @var bool
     */
    protected $admin = false;
}