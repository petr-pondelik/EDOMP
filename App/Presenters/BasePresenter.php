<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.2.19
 * Time: 21:53
 */

namespace App\Presenters;

use App\Arguments\UserInformArgs;
use App\Components\HeaderBar\HeaderBarControl;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarControl;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\FlashesTranslator;
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
     * @var FlashesTranslator
     */
    protected $flashesTranslator;

    /**
     * BasePresenter constructor.
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     */
    public function __construct
    (
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator
    )
    {
        parent::__construct();
        $this->headerBarFactory = $headerBarFactory;
        $this->sideBarFactory = $sideBarFactory;
        $this->flashesTranslator = $flashesTranslator;
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
     * @param UserInformArgs $args
     */
    public function informUser(UserInformArgs $args): void
    {
        $message = $this->flashesTranslator::translate($args->operation, $this->getName(), $args->type, $args->exception);

        if($args->type === 'success'){
            if(!$args->component)
                $this->flashMessage($message, 'success');
            else
                $this[$args->component]->flashMessage($message, 'success');
        }
        else{
            if(!$args->component)
                $this->flashMessage($message, 'danger');
            else
                $this[$args->component]->flashMessage($message, 'danger');
        }

        if($args->ajax){
            if($args->main){
                if(!$args->component)
                    $this->redrawControl('mainFlashesSnippet');
                else
                    $this[$args->component]->redrawControl('mainFlashesSnippet');
            }
            else{
                if(!$args->component)
                    $this->redrawControl('flashesSnippet');
                else
                    $this[$args->component]->redrawControl('flashesSnippet');
            }
        }
    }
}