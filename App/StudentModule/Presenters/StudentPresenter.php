<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 14.4.19
 * Time: 19:12
 */

namespace App\StudentModule\Presenters;

use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Presenters\SecuredPresenter;
use App\CoreModule\Services\Authorizator;

/**
 * Class StudentPresenter
 * @package App\StudentModule\Presenters
 */
abstract class StudentPresenter extends SecuredPresenter
{
    /**
     * @var Authorizator
     */
    protected $authorizator;

    /**
     * StudentPresenter constructor.
     * @param Authorizator $authorizator
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     */
    public function __construct
    (
        Authorizator $authorizator,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator
    )
    {
        parent::__construct($headerBarFactory, $sideBarFactory, $flashesTranslator);
        $this->authorizator = $authorizator;
    }

    /**
     * @throws \Nette\Application\AbortException
     */
    public function secure(): void
    {
        if (!$this->user->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }
}