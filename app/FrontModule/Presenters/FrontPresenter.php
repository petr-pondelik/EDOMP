<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 14.4.19
 * Time: 19:12
 */

namespace App\FrontModule\Presenters;

use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\ISideBarFactory;
use App\Helpers\FlashesTranslator;
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
     * @param HeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     */
    public function __construct
    (
        Authorizator $authorizator,
        HeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator
    )
    {
        parent::__construct($headerBarFactory, $sideBarFactory, $flashesTranslator);
        $this->authorizator = $authorizator;
    }

    /**
     * @throws \Nette\Application\AbortException
     */
    public function startup(): void
    {
        parent::startup();
        if(!$this->user->isLoggedIn()){
            $this->redirect('Sign:in');
        }
    }
}