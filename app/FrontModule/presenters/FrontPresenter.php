<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 14.4.19
 * Time: 19:12
 */

namespace App\FrontModule\Presenters;

use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarFactory;
use App\Presenters\BasePresenter;
use App\Service\Authorizator;

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
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     */
    public function __construct
    (
        Authorizator $authorizator,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory
    )
    {
        parent::__construct($headerBarFactory, $sideBarFactory);
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