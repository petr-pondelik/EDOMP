<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 20.3.19
 * Time: 19:10
 */

namespace App\AdminModule\Presenters;


use App\Presenters\BasePresenter;

/**
 * Class AdminPresenter
 * @package App\AdminModule\Presenters
 */
class AdminPresenter extends BasePresenter
{
    /**
     * @throws \Nette\Application\AbortException
     */
    public function startup()
    {
        parent::startup();
        if(!($this->user->isInRole("admin"))){
            if($this->user->isLoggedIn())
                $this->flashMessage("Nedostatečná přístupová práva.", "danger");
            $this->redirect('Sign:in');
        }
    }
}