<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 12:34
 */

namespace App\AdminModule\Presenters;


use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarFactory;
use App\Exceptions\EntityException;
use App\Helpers\FlashesTranslator;
use App\Model\Entity\Category;
use App\Model\Entity\Difficulty;
use App\Model\Entity\Group;
use App\Model\Entity\ProblemFinal;
use App\Model\Entity\ProblemType;
use App\Model\Entity\QuadraticEqTempl;
use App\Model\Entity\SubCategory;
use App\Model\Entity\Test;
use App\Model\Manager\ConstraintEntityManager;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemFinalRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\QuadraticEqTemplRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use App\Presenters\BasePresenter;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\DateTime;
use Nette\Utils\Json;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DoctrinePresenter extends AdminPresenter
{
    /**
     * @var ProblemFinalRepository
     */
    protected $problemRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var TemplateJsonDataRepository
     */
    protected $templateJsonDataRepository;

    /**
     * @var
     */
    protected $problemTemplateRepository;

    /**
     * @var QuadraticEqTemplRepository
     */
    protected $quadraticEqTemplRepository;

    /**
     * @var ConstraintEntityManager
     */
    protected $em;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * DoctrinePresenter constructor.
     * @param Authorizator $authorizator
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param ConstraintEntityManager $em
     * @param ProblemFinalRepository $problemRepository
     * @param CategoryRepository $categoryRepository
     * @param TemplateJsonDataRepository $templateJsonDataRepository
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param QuadraticEqTemplRepository $quadraticEqTemplRepository
     * @param ValidatorInterface $validator
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        ConstraintEntityManager $em, ProblemFinalRepository $problemRepository, CategoryRepository $categoryRepository,
        TemplateJsonDataRepository $templateJsonDataRepository,
        ProblemTemplateRepository $problemTemplateRepository, QuadraticEqTemplRepository $quadraticEqTemplRepository,
        ValidatorInterface $validator
    )
    {
        parent::__construct($authorizator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator);
        $this->problemRepository = $problemRepository;
        $this->categoryRepository = $categoryRepository;
        $this->templateJsonDataRepository = $templateJsonDataRepository;
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->quadraticEqTemplRepository = $quadraticEqTemplRepository;
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * @throws EntityException
     */
    public function actionDefault()
    {
        $category = new Category();
        $category->setLabel("TESTCATEGORY1");
        $this->em->persist($category);

        $difficulty = new Difficulty();
        $difficulty->setLabel("TESTDIFFICULTY1");
        $this->em->persist($difficulty);


        /*$group = new Group();
        $this->em->persist($group);*/

        $subCategory = new SubCategory();
        $subCategory->setLabel("TESTSUBCATEGORY1");
        $subCategory->setCategory($category);
        $this->em->persist($subCategory);

        $problemType = new ProblemType();
        $problemType->setAccessor(7);
        $problemType->setLabel("Funkce");
        $this->em->persist($problemType);

        $problemFinal = new ProblemFinal();
        $problemFinal->setBody("$$ 15 x + 20 = 0 $$");
        $problemFinal->setDifficulty($difficulty);
        $problemFinal->setVariable("x");
        $problemFinal->setProblemType($problemType);
        $problemFinal->setSubCategory($subCategory);
        $this->em->persist($problemFinal);

        $test = new Test();
        $test->setTestNumber(-10);
        $test->setSchoolYear("2018-19");
        $this->em->persist($test);

        /*$this->em->persist($category);*/


    }
}