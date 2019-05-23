<?php

namespace App\AdminModule\Presenters;

use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarFactory;
use App\Model\Entity\Category;
use App\Model\Entity\Group;
use App\Model\Entity\Logo;
use App\Model\Entity\ProblemFinal;
use App\Model\Entity\ProblemTemplate;
use App\Model\Entity\SubCategory;
use App\Model\Entity\SuperGroup;
use App\Model\Entity\Test;
use App\Model\Entity\User;
use App\Model\Manager\HomepageStatisticsManager;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\ProblemFinalRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TestRepository;
use App\Model\Repository\UserRepository;
use App\Service\Authorizator;
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
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param ProblemFinalRepository $problemFinalRepository
     * @param CategoryRepository $categoryRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param TestRepository $testRepository
     * @param UserRepository $userRepository
     * @param HomepageStatisticsManager $homepageStatisticsManager
     */
    public function __construct
    (
        Authorizator $authorizator,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory,
        ProblemTemplateRepository $problemTemplateRepository, ProblemFinalRepository $problemFinalRepository,
        CategoryRepository $categoryRepository, SubCategoryRepository $subCategoryRepository,
        TestRepository $testRepository, UserRepository $userRepository, HomepageStatisticsManager $homepageStatisticsManager
    )
    {
        parent::__construct($authorizator, $headerBarFactory, $sideBarFactory);
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
     */
    public function startup()
    {
        parent::startup();
        $this->getStats();
    }

    public function getStats()
    {
        /*$this->template->problemPrototypeCnt = $this->homepageStatisticsManager->getProblemPrototypeCnt();
        $this->template->problemFinalCnt = $this->homepageStatisticsManager->getProblemFinalCnt();
        $this->template->categoryCnt = $this->homepageStatisticsManager->getCategoryCnt();
        $this->template->subCategoryCnt = $this->homepageStatisticsManager->getSubCategoryCnt();
        $this->template->testCnt = $this->homepageStatisticsManager->getTestCnt();
        $this->template->userCnt = $this->homepageStatisticsManager->getUserCnt();
        $this->template->groupCnt = $this->homepageStatisticsManager->getGroupCnt();
        $this->template->superGroupCnt = $this->homepageStatisticsManager->getSuperGroupCnt();
        $this->template->logoCnt = $this->homepageStatisticsManager->getLogoCnt();*/

        //$this->template->problemTemplateCnt = $this->problemTemplateRepository->getCnt();

        $this->template->problemTemplateCnt = $this->homepageStatisticsManager->getCnt(ProblemTemplate::class);
        $this->template->problemFinalCnt = $this->homepageStatisticsManager->getCnt(ProblemFinal::class);
        $this->template->categoryCnt = $this->homepageStatisticsManager->getCnt(Category::class);
        $this->template->subCategoryCnt = $this->homepageStatisticsManager->getCnt(SubCategory::class);
        $this->template->testCnt = $this->homepageStatisticsManager->getCnt(Test::class);
        $this->template->userCnt = $this->homepageStatisticsManager->getCnt(User::class);
        $this->template->groupCnt = $this->homepageStatisticsManager->getCnt(Group::class);
        $this->template->superGroupCnt = $this->homepageStatisticsManager->getCnt(SuperGroup::class);
        $this->template->logoCnt = $this->homepageStatisticsManager->getCnt(Logo::class);

        /*$this->template->problemFinalCnt = $this->problemFinalRepository->getCnt();
        $this->template->categoryCnt = $this->categoryRepository->getCnt();
        $this->template->subCategoryCnt = $this->subCategoryRepository->getCnt();
        $this->template->testCnt = $this->testRepository->getCnt();*/

    }
}
