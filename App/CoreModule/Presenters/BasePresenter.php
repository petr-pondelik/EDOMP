<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.2.19
 * Time: 21:53
 */

namespace App\CoreModule\Presenters;

use App\CoreModule\Arguments\UserInformArgs;
use App\CoreModule\Components\FlashesModal\FlashesModalControl;
use App\CoreModule\Components\FlashesModal\IFlashesModalFactory;
use App\CoreModule\Components\HeaderBar\HeaderBarControl;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\SideBar\SideBarControl;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use Nette\Application\Helpers;
use Nette\Application\UI\Presenter;
use Nette\Utils\Strings;


/**
 * Class BasePresenter
 * @package App\CoreModule\Presenters
 */
abstract class BasePresenter extends Presenter
{
    /**
     * @var IHeaderBarFactory
     */
    protected $headerBarFactory;

    /**
     * @var ISideBarFactory
     */
    protected $sideBarFactory;

    /**
     * @var IFlashesModalFactory
     */
    protected $flashesModalFactory;

    /**
     * @var FlashesTranslator
     */
    protected $flashesTranslator;

    /**
     * BasePresenter constructor.
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     */
    public function __construct
    (
        IHeaderBarFactory $headerBarFactory,
        ISideBarFactory $sideBarFactory,
        FlashesTranslator $flashesTranslator
    )
    {
        parent::__construct();
        $this->headerBarFactory = $headerBarFactory;
        $this->sideBarFactory = $sideBarFactory;
        $this->flashesTranslator = $flashesTranslator;
    }

    /**
     * @return string
     */
    public function getModuleName(): string
    {
        return Helpers::splitName($this->getName())[0];
    }

    /**
     * @param string $moduleName
     * @return bool
     */
    public function isModule(string $moduleName): bool
    {
        return $this->getModuleName() === $moduleName;
    }

    /**
     * @return bool
     */
    public function isProblemTemplatePresenter(): bool
    {
        return (bool)Strings::match($this->getName(), '~^Teacher:.*Template$~');
    }

    public function beforeRender(): void
    {
        parent::beforeRender();
        $this->redrawControl('mathJaxRender');
    }

    /**
     * @param IFlashesModalFactory $flashesModalFactory
     */
    public function injectFlashesModalFactory(IFlashesModalFactory $flashesModalFactory): void
    {
        $this->flashesModalFactory = $flashesModalFactory;
    }

    /**
     * @return \App\CoreModule\Components\HeaderBar\HeaderBarControl
     */
    public function createComponentHeaderBar(): HeaderBarControl
    {
        return $this->headerBarFactory->create();
    }

    /**
     * @return \App\CoreModule\Components\SideBar\SideBarControl
     */
    public function createComponentSideBar(): SideBarControl
    {
        return $this->sideBarFactory->create();
    }

    /**
     * @return FlashesModalControl
     */
    public function createComponentFlashesModal(): FlashesModalControl
    {
        return $this->flashesModalFactory->create();
    }

    /**
     * @param UserInformArgs $args
     * @throws \App\CoreModule\Exceptions\FlashesTranslatorException
     */
    public function informUser(UserInformArgs $args): void
    {
        if (!$args->message) {
            $message = $this->flashesTranslator::translate($args->operation, $this->getName(), $args->type, $args->exception);
        } else {
            $message = $args->message;
        }

        if ($args->type === 'success') {
            if (!$args->component) {
                $this->flashMessage($message, 'success');
            } else {
                $this[$args->component]->flashMessage($message, 'success');
            }
        } else {
            if (!$args->component) {
                $this->flashMessage($message, 'danger');
            } else {
                $this[$args->component]->flashMessage($message, 'danger');
            }
        }

        if ($args->ajax) {
            if (!$args->component) {
                $this->redrawControl('mainFlashesSnippet');
                $this->redrawControl('flashesSnippet');
            } else {
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
     * @param string $key
     * @param bool $status
     * @param array|null $data
     */
    public function setPayload(string $key = 'response', bool $status = true, array $data = null): void
    {
        $this->presenter->payload->{$key} = [
            'status' => $status,
            'data' => $data
        ];
    }
}