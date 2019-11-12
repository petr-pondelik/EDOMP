<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:06
 */

namespace App\TeacherModule\Presenters;


use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\HelpModal\HelpModalControl;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Presenters\BasePresenter;
use App\CoreModule\Services\Authorizator;
use App\TeacherModule\Services\NewtonApiClient;
use App\CoreModule\Services\Validator;

/**
 * Class TeacherPresenter
 * @package App\TeacherModule\Presenters
 */
abstract class TeacherPresenter extends BasePresenter
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
     * @var IHelpModalFactory
     */
    protected $sectionHelpModalFactory;

    /**
     * TeacherPresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param IHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        IHelpModalFactory $sectionHelpModalFactory
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
     * @return HelpModalControl
     */
    public function createComponentSectionHelpModal(): HelpModalControl
    {
        return $this->sectionHelpModalFactory->create();
    }
}