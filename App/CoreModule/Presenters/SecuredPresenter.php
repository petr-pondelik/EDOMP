<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.11.19
 * Time: 17:01
 */

namespace App\CoreModule\Presenters;


use App\CoreModule\Interfaces\ISecuredPresenter;

/**
 * Class SecuredPresenter
 * @package App\CoreModule\Presenters
 */
abstract class SecuredPresenter extends BasePresenter implements ISecuredPresenter
{
    public function startup(): void
    {
        parent::startup();
        $this->secure();
    }
}