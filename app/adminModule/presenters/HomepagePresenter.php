<?php

namespace App\AdminModule\Presenters;

use App\Model\Managers\HomepageStatisticsManager;
use Nette;

/**
 * Class HomepagePresenter
 * @package App\AdminModule\Presenters
 */
final class HomepagePresenter extends AdminPresenter
{
    /**
     * @var HomepageStatisticsManager
     */
    protected $homepageStatisticsManager;

    /**
     * HomepagePresenter constructor.
     * @param HomepageStatisticsManager $homepageStatisticsManager
     */
    public function __construct
    (
        HomepageStatisticsManager $homepageStatisticsManager
    )
    {
        parent::__construct();
        $this->homepageStatisticsManager = $homepageStatisticsManager;
    }

    /**
     * @throws Nette\Application\AbortException
     */
    public function startup()
    {
        parent::startup();
        $this->getStats();
    }

    public function getStats()
    {
        $this->template->problemPrototypeCnt = $this->homepageStatisticsManager->getProblemPrototypeCnt();
        $this->template->problemFinalCnt = $this->homepageStatisticsManager->getProblemFinalCnt();
        $this->template->categoryCnt = $this->homepageStatisticsManager->getCategoryCnt();
        $this->template->subCategoryCnt = $this->homepageStatisticsManager->getSubCategoryCnt();
        $this->template->testCnt = $this->homepageStatisticsManager->getTestCnt();
        $this->template->userCnt = $this->homepageStatisticsManager->getUserCnt();
        $this->template->groupCnt = $this->homepageStatisticsManager->getGroupCnt();
        $this->template->superGroupCnt = $this->homepageStatisticsManager->getSuperGroupCnt();
        $this->template->logoCnt = $this->homepageStatisticsManager->getLogoCnt();
    }
}
