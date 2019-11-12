<?php

namespace App\TeacherModule\Presenters;

use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Entity\Category;
use App\CoreModule\Model\Persistent\Entity\Group;
use App\CoreModule\Model\Persistent\Entity\Logo;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\CoreModule\Model\Persistent\Entity\SubCategory;
use App\CoreModule\Model\Persistent\Entity\SuperGroup;
use App\CoreModule\Model\Persistent\Entity\Test;
use App\CoreModule\Model\Persistent\Entity\User;
use App\CoreModule\Model\Persistent\Manager\HomepageStatisticsManager;
use App\CoreModule\Model\Persistent\Repository\CategoryRepository;
use App\CoreModule\Model\Persistent\Repository\GroupRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemFinal\ProblemFinalRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;
use App\CoreModule\Model\Persistent\Repository\SubCategoryRepository;
use App\CoreModule\Model\Persistent\Repository\TestRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use App\CoreModule\Services\Authorizator;
use App\TeacherModule\Services\NewtonApiClient;
use App\CoreModule\Services\Validator;
use Nette;

/**
 * Class HomepagePresenter
 * @package App\TeacherModule\Presenters
 */
final class HomepagePresenter extends TeacherPresenter
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
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param ProblemFinalRepository $problemFinalRepository
     * @param CategoryRepository $categoryRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param TestRepository $testRepository
     * @param UserRepository $userRepository
     * @param HomepageStatisticsManager $homepageStatisticsManager
     * @param IHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        ProblemTemplateRepository $problemTemplateRepository, ProblemFinalRepository $problemFinalRepository,
        CategoryRepository $categoryRepository, SubCategoryRepository $subCategoryRepository,
        TestRepository $testRepository, UserRepository $userRepository, HomepageStatisticsManager $homepageStatisticsManager,
        IHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct($authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory);
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
