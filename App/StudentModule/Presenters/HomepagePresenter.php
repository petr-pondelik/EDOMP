<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 20.3.19
 * Time: 20:26
 */

namespace App\StudentModule\Presenters;

use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Manager\HomepageStatisticsManager;
use App\CoreModule\Services\Authorizator;

/**
 * Class HomepagePresenter
 * @package App\StudentModule\Presenters
 */
final class HomepagePresenter extends StudentPresenter
{
    /**
     * @var HomepageStatisticsManager
     */
    private $statisticsManager;

    /**
     * HomepagePresenter constructor.
     * @param Authorizator $authorizator
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param HomepageStatisticsManager $statisticsManager
     */
    public function __construct
    (
        Authorizator $authorizator,
        IHeaderBarFactory $headerBarFactory,
        ISideBarFactory $sideBarFactory,
        FlashesTranslator $flashesTranslator,
        HomepageStatisticsManager $statisticsManager
    )
    {
        parent::__construct($authorizator, $headerBarFactory, $sideBarFactory, $flashesTranslator);
        $this->statisticsManager = $statisticsManager;
    }

    /**
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function renderDefault(): void
    {
        $this->getStats();
    }

    /**
     * @throws \Doctrine\ORM\Query\QueryException
     */
    private function getStats(): void
    {
        $this->template->statistics = $this->statisticsManager->getThemesCnt($this->getUser());
    }
}