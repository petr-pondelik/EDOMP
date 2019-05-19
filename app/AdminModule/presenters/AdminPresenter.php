<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:06
 */

namespace App\AdminModule\Presenters;


use App\Presenters\BasePresenter;
use App\Service\Authorizator;

/**
 * Class AdminPresenter
 * @package App\AdminModule\Presenters
 */
abstract class AdminPresenter extends BasePresenter
{
    /**
     * @var Authorizator
     */
    protected $authorizator;

    /**
     * AdminPresenter constructor.
     * @param Authorizator $authorizator
     */
    public function __construct
    (
        Authorizator $authorizator
    )
    {
        parent::__construct();
        $this->authorizator = $authorizator;
    }

    /**
     * @throws \Nette\Application\AbortException
     */
    public function startup()
    {
        parent::startup();
        if(!(
            $this->user->isInRole("admin") || $this->user->isInRole("teacher")
        )){
            if($this->user->isLoggedIn())
                $this->flashMessage("Nedostatečná přístupová práva.", "danger");
            $this->redirect('Sign:in');
        }
    }
}