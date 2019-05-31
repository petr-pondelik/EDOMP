<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:06
 */

namespace App\AdminModule\Presenters;


use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Presenters\BasePresenter;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;

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
     * @var NewtonApiClient
     */
    protected $newtonApiClient;

    /**
     * AdminPresenter constructor.
     * @param Authorizator $authorizator
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator
    )
    {
        parent::__construct($headerBarFactory, $sideBarFactory, $flashesTranslator);
        $this->authorizator = $authorizator;
        $this->newtonApiClient = $newtonApiClient;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
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
        $this->template->newtonApiConnection = $this->newtonApiClient->ping() ;
    }
}