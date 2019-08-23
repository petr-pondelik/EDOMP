<?php

namespace App\AdminModule\Presenters;

use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Model\Persistent\Entity\Category;
use App\Model\Persistent\Entity\Group;
use App\Model\Persistent\Entity\Logo;
use App\Model\Persistent\Entity\ProblemFinal;
use App\Model\Persistent\Entity\ProblemTemplate;
use App\Model\Persistent\Entity\SubCategory;
use App\Model\Persistent\Entity\SuperGroup;
use App\Model\Persistent\Entity\Test;
use App\Model\Persistent\Entity\User;
use App\Model\Persistent\Manager\HomepageStatisticsManager;
use App\Model\Persistent\Repository\CategoryRepository;
use App\Model\Persistent\Repository\GroupRepository;
use App\Model\Persistent\Repository\ProblemFinalRepository;
use App\Model\Persistent\Repository\ProblemTemplateRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Model\Persistent\Repository\TestRepository;
use App\Model\Persistent\Repository\UserRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use Nette;

/**
 * Class HomepagePresenter
 * @package App\AdminModule\Presenters
 */
final class HomepagePresenter extends AdminPresenter
{
    /**
     * @var ProblemTemplateRepository
     */
    protected $problemTemplateRepository;

    /**
     * @var ProblemFinalRepository
     */
    protected $problemFinalRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var SubCategoryRepository
     */
    protected $subCategoryRepository;

    /**
     * @var TestRepository
     */
    protected $testRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var HomepageStatisticsManager
     */
    protected $homepageStatisticsManager;

    /**
     * HomepagePresenter constructor.
     * @param Authorizator $authorizator
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param ProblemFinalRepository $problemFinalRepository
     * @param CategoryRepository $categoryRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param TestRepository $testRepository
     * @param UserRepository $userRepository
     * @param HomepageStatisticsManager $homepageStatisticsManager
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        ProblemTemplateRepository $problemTemplateRepository, ProblemFinalRepository $problemFinalRepository,
        CategoryRepository $categoryRepository, SubCategoryRepository $subCategoryRepository,
        TestRepository $testRepository, UserRepository $userRepository, HomepageStatisticsManager $homepageStatisticsManager,
        ISectionHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct($authorizator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory);
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->problemFinalRepository = $problemFinalRepository;
        $this->categoryRepository = $categoryRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->testRepository = $testRepository;
        $this->userRepository = $userRepository;
        $this->homepageStatisticsManager = $homepageStatisticsManager;
    }

    /**
     * @throws Nette\Application\AbortException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function startup(): void
    {
        parent::startup();
        $this->getStats();
    }

    public function getStats(): void
    {
        $this->template->problemTemplateCnt = $this->homepageStatisticsManager->getCnt(ProblemTemplate::class);
        $this->template->problemFinalCnt = $this->homepageStatisticsManager->getCnt(ProblemFinal::class);
        $this->template->categoryCnt = $this->homepageStatisticsManager->getCnt(Category::class);
        $this->template->subCategoryCnt = $this->homepageStatisticsManager->getCnt(SubCategory::class);
        $this->template->testCnt = $this->homepageStatisticsManager->getCnt(Test::class);
        $this->template->userCnt = $this->homepageStatisticsManager->getCnt(User::class);
        $this->template->groupCnt = $this->homepageStatisticsManager->getCnt(Group::class);
        $this->template->superGroupCnt = $this->homepageStatisticsManager->getCnt(SuperGroup::class);
        $this->template->logoCnt = $this->homepageStatisticsManager->getCnt(Logo::class);
    }
}
