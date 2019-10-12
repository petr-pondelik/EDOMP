<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:06
 */

namespace App\AdminModule\Presenters;


use App\Components\HeaderBar\IHeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SectionHelpModal\SectionHelpModalControl;
use App\Components\SideBar\ISideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Presenters\BasePresenter;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use App\Services\Validator;

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
     * @var Validator
     */
    protected $validator;

    /**
     * @var NewtonApiClient
     */
    protected $newtonApiClient;

    /**
     * @var ISectionHelpModalFactory
     */
    protected $sectionHelpModalFactory;

    /**
     * AdminPresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        ISectionHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct($headerBarFactory, $sideBarFactory, $flashesTranslator);
        $this->authorizator = $authorizator;
        $this->validator = $validator;
        $this->newtonApiClient = $newtonApiClient;
        $this->sectionHelpModalFactory = $sectionHelpModalFactory;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Nette\Application\AbortException
     */
    public function startup(): void
    {
        parent::startup();
        if(!($this->user->isInRole('admin') || $this->user->isInRole("teacher"))){
            if($this->user->isLoggedIn()){
                $this->flashMessage("Nedostatečná přístupová práva.", "danger");
            }
            $this->redirect('Sign:in');
        }
        $this->template->newtonApiConnection = $this->newtonApiClient->ping();
    }

    /**
     * @return SectionHelpModalControl
     */
    public function createComponentSectionHelpModal(): SectionHelpModalControl
    {
        return $this->sectionHelpModalFactory->create();
    }
}