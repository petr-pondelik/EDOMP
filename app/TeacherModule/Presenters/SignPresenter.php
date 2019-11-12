<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 17:24
 */

namespace App\TeacherModule\Presenters;


use App\CoreModule\Presenters\BaseSignPresenter;

/**
 * Class SignPresenter
 * @package App\TeacherModule\Presenters
 */
class SignPresenter extends BaseSignPresenter
{
    /**
     * @var bool
     */
    protected $admin = true;
}