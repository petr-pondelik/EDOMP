<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 14.4.19
 * Time: 19:12
 */

namespace App\FrontModule\Presenters;

use App\Presenters\BasePresenter;
use App\Services\Authorizator;

/**
 * Class FrontPresenter
 * @package App\FrontModule\Presenters
 */
class FrontPresenter extends BasePresenter
{
    /**
     * @var Authorizator
     */
    protected $authorizator;

    /**
     * FrontPresenter constructor.
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
        if(!($this->user->isLoggedIn()))
            $this->redirect('Sign:in');
    }
}