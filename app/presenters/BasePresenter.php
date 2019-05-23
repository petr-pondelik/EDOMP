<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.2.19
 * Time: 21:53
 */

namespace App\Presenters;

use App\Components\HeaderBar\HeaderBarControl;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarControl;
use App\Components\SideBar\SideBarFactory;
use Nette\Application\UI\Presenter;


/**
 * Class BasePresenter
 * @package App\Presenters
 */
class BasePresenter extends Presenter
{
    /**
     * @var HeaderBarFactory
     */
    protected $headerBarFactory;

    /**
     * @var SideBarFactory
     */
    protected $sideBarFactory;

    /**
     * BasePresenter constructor.
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     */
    public function __construct
    (
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory
    )
    {
        parent::__construct();
        $this->headerBarFactory = $headerBarFactory;
        $this->sideBarFactory = $sideBarFactory;

    }

    public function beforeRender(): void
    {
        parent::beforeRender();
        $this->redrawControl('mathJaxRender');
    }

    /**
     * @return \App\Components\HeaderBar\HeaderBarControl
     */
    public function createComponentHeaderBar(): HeaderBarControl
    {
        return $this->headerBarFactory->create();
    }

    /**
     * @return \App\Components\SideBar\SideBarControl
     */
    public function createComponentSideBar(): SideBarControl
    {
        return $this->sideBarFactory->create();
    }

    /**
     * @param string $message
     * @param bool $ajax
     * @param string $type
     * @param \Exception|null $exception
     * @param bool $main
     */
    public function informUser(string $message, bool $ajax = false, string $type = 'success', \Exception $exception = null, bool $main = false): void
    {
        $this->flashMessage($message, $type);
        if($ajax){
            if($main)
                $this->redrawControl('mainFlashesSnippet');
            else
                $this->redrawControl('flashesSnippet');
        }
    }
}