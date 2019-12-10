<?php

namespace App\TeacherModule\Presenters;

use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Manager\HomepageStatisticsManager;
use App\CoreModule\Services\Authorizator;
use App\TeacherModule\Services\NewtonApiClient;
use App\CoreModule\Services\Validator;
use Nette\Security\User;

/**
 * Class HomepagePresenter
 * @package App\TeacherModule\Presenters
 */
final class HomepagePresenter extends TeacherPresenter
{
    /**
     * @var HomepageStatisticsManager
     */
    protected $homepageStatisticsManager;

    /**
     * @var User
     */
    protected $user;

    /**
     * HomepagePresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param IHelpModalFactory $sectionHelpModalFactory
     * @param HomepageStatisticsManager $homepageStatisticsManager
     * @param User $user
     */
    public function __construct
    (
        Authorizator $authorizator,
        Validator $validator,
        NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory,
        ISideBarFactory $sideBarFactory,
        FlashesTranslator $flashesTranslator,
        IHelpModalFactory $sectionHelpModalFactory,
        HomepageStatisticsManager $homepageStatisticsManager,
        User $user
    )
    {
        parent::__construct($authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory);
        $this->homepageStatisticsManager = $homepageStatisticsManager;
        $this->user = $user;
    }

    /**
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \App\TeacherModule\Exceptions\HomepageStatisticsException
     */
    public function renderDefault(): void
    {
        $this->getStats();
    }

    /**
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \App\TeacherModule\Exceptions\HomepageStatisticsException
     */
    public function getStats(): void
    {
        $this->template->statistics = $this->homepageStatisticsManager->getHomepageStatistics($this->user);
    }
}
