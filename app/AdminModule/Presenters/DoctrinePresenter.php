<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 12:34
 */

namespace App\AdminModule\Presenters;


use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\SideBarFactory;
use App\Exceptions\EntityException;
use App\Helpers\FlashesTranslator;
use App\Helpers\LatexHelper;
use App\Helpers\StringsHelper;
use App\Model\Entity\ArithmeticSeqTempl;
use App\Model\Entity\Category;
use App\Model\Entity\Difficulty;
use App\Model\Entity\Group;
use App\Model\Entity\LinearEqTempl;
use App\Model\Entity\Logo;
use App\Model\Entity\ProblemCondition;
use App\Model\Entity\ProblemConditionType;
use App\Model\Entity\ProblemFinal;
use App\Model\Entity\ProblemFinalTestVariantAssociation;
use App\Model\Entity\ProblemType;
use App\Model\Entity\QuadraticEqTempl;
use App\Model\Entity\Role;
use App\Model\Entity\SubCategory;
use App\Model\Entity\Test;
use App\Model\Entity\TestVariant;
use App\Model\Entity\User;
use App\Model\Functionality\TestFunctionality;
use App\Model\Functionality\TestVariantFunctionality;
use App\Model\Manager\ConstraintEntityManager;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemFinalRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\QuadraticEqTemplRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use App\Model\Repository\TestRepository;
use App\Presenters\BasePresenter;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use App\Services\Validator;
use jlawrence\eos\Parser;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;
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
     * @var LatexHelper
     */
    protected $latexHelper;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

//    /**
//     * @var Validator
//     */
//    protected $validator;

    /**
     * @var TestFunctionality
     */
    protected $testFunctionality;

    /**
     * @var TestVariantFunctionality
     */
    protected $testVariantFunctionality;

    /**
     * @var TestRepository
     */
    protected $testRepository;

    /**
     * @var Parser
     */
    protected $parser;

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
     * @param LatexHelper $latexHelper
     * @param StringsHelper $stringsHelper
     * @param Validator $validator
     * @param TestVariantFunctionality $testVariantFunctionality
     * @param TestFunctionality $testFunctionality
     * @param TestRepository $testRepository
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        ConstraintEntityManager $em, ProblemFinalRepository $problemRepository, CategoryRepository $categoryRepository,
        TemplateJsonDataRepository $templateJsonDataRepository,
        ProblemTemplateRepository $problemTemplateRepository, QuadraticEqTemplRepository $quadraticEqTemplRepository,
        ValidatorInterface $validator,
        LatexHelper $latexHelper, StringsHelper $stringsHelper, TestVariantFunctionality $testVariantFunctionality,
        TestFunctionality $testFunctionality,
        TestRepository $testRepository,
        ISectionHelpModalFactory $sectionHelpModalFactory,
        Parser $parser
    )
    {
        parent::__construct($authorizator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory);
        $this->problemRepository = $problemRepository;
        $this->categoryRepository = $categoryRepository;
        $this->templateJsonDataRepository = $templateJsonDataRepository;
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->quadraticEqTemplRepository = $quadraticEqTemplRepository;
        $this->em = $em;
        $this->validator = $validator;
        $this->latexHelper = $latexHelper;
        $this->stringsHelper = $stringsHelper;
        $this->testVariantFunctionality = $testVariantFunctionality;
        $this->testFunctionality = $testFunctionality;
        $this->testRepository = $testRepository;
        $this->parser = $parser;
    }

    /**
     * @throws \Exception
     */
    public function actionDefault()
    {
        bdump($this->parser::solve('5+4(1+2)+3+ln(e)'));
        bdump($this->parser::solve('0^2 - 4 * 1/2 log(3) * (1/2 0 - 1/5 log(100))'));

        bdump($this->parser::solve('2^(1 - 2) * 3^(-1 + 2)'));

//        $test = $this->testFunctionality->create(ArrayHash::from([
//            'logo_id' => 1,
//            'term' => '1. pol.',
//            'school_year' => '2018/19',
//            'test_number' => 1,
//            'groups' => [1],
//            'introduction_text' => ''
//        ]));
//
//        $testVariant = $this->testVariantFunctionality->create(ArrayHash::from([
//            'variantLabel' => 'TEST LABEL',
//            'test' => $test
//        ]));
//
//        bdump($testVariant);
//
//        $test->addTestVariant($testVariant);
//
//        $this->em->persist($testVariant);
//        $this->em->flush();

//        $category = new Category();
//        $category->setLabel("TESTCATEGORY1");
//        //$this->em->persist($category);
//
//        $difficulty = new Difficulty();
//        $difficulty->setLabel("TESTDIFFICULTY1");
//        //$this->em->persist($difficulty);
//
//
//        /*$group = new Group();
//        $this->em->persist($group);*/
//
//        $subCategory = new SubCategory();
//        $subCategory->setLabel("TESTSUBCATEGORY1");
//        $subCategory->setCategory($category);
//        //$this->em->persist($subCategory);
//
//        /*$entity = new ProblemConditionType();
//        $errors = $this->validator->validate($entity);
//        bdump($errors);*/
//
//        $problemType = new ProblemType();
//        $problemType->setLabel("Funkce");

//        bdump($this->validator->validateLinearEquation('55/15 x - 42/22 + 2/3 p2 - 43/20 p2 + 4 p3 + 15 p4', 'x'));
//        bdump($this->latexHelper::parseLatex('$$ x^{2} $$'));
//        bdump($this->latexHelper::parseLatex('$$ \frac{1}{2} \big( 2 x - 1 \big)^2 - \big( \frac{1}{2} \big( x + 1 \big) \big)^2 = 3 \big( \big( \frac{1}{2} x \big)^2 - \big( \frac{1}{<par min="2" max="10"/>} \big)^2 \big)^{2} $$'));
//
//        bdump($this->stringsHelper::getLinearEquationRegExp('x'));
//        bdump($this->stringsHelper::getQuadraticEquationRegExp('x'));
//        bdump($this->templateJsonDataRepository->find(35));

        /*$entity = new ArithmeticSeqTempl();
        $errors = $this->validator->validate($entity);
        bdump($errors);*/

        /*$entity = new ProblemCondition();
        $errors = $this->validator->validate($entity);
        bdump($errors);*/

        /*$res = $this->categoryRepository->delete(500);
        bdump($res);*/

        /*$problemFinal = new ProblemFinal();
        $problemFinal->setBody("$$ 15 x + 20 = 0 $$");
        $problemFinal->setDifficulty($difficulty);
        $problemFinal->setVariable("x");
        $problemFinal->setProblemType($problemType);
        $problemFinal->setSubCategory($subCategory);
        $this->em->persist($problemFinal);

        $test = new Test();
        $test->setTestNumber(-10);
        $test->setSchoolYear("2018-19");
        $this->em->persist($test);*/

        /*$this->em->persist($category);*/

        //bdump($this->latexHelper::parseLatex('$$ \bigg \langle15 x + 5\bigg \rangle $$'));

    }
}