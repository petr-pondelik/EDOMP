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
use App\Components\SideBar\ISideBarFactory;
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
     * @var ISideBarFactory
     */
    protected $sideBarFactory;

    /**
     * @var FlashesTranslator
     */
    protected $flashesTranslator;

    /**
     * BasePresenter constructor.
     * @param HeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     */
    public function __construct
    (
        HeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator
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
            if(!$args->component) {
                $this->flashMessage($message, 'success');
            }
            else{
                $this[$args->component]->flashMessage($message, 'success');
            }
        }
        else{
            if(!$args->component) {
                $this->flashMessage($message, 'danger');
            }
            else{
                $this[$args->component]->flashMessage($message, 'danger');
            }
        }

        if($args->ajax){
            if(!$args->component){
                $this->redrawControl('mainFlashesSnippet');
                $this->redrawControl('flashesSnippet');
            }
            else{
                $this[$args->component]->redrawControl('mainFlashesSnippet');
                $this[$args->component]->redrawControl('flashesSnippet');
            }
        }
    }

    /**
     * @param bool $status
     * @param array|null $data
     * @throws \Nette\Application\AbortException
     */
    public function sendJsonResponse(bool $status, array $data = null): void
    {
        $this->sendJson([
            'status' => $status,
            'data' => $data
        ]);
    }

    /**
     * @param bool $status
     * @param array|null $data
     */
    public function setPayload(bool $status, array $data = null): void
    {
        $this->presenter->payload->response = [
            'status' => $status,
            'data' => $data
        ];
    }
}