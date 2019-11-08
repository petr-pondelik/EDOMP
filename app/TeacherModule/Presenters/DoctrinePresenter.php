<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 12:34
 */

namespace App\TeacherModule\Presenters;


use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Helpers\LatexHelper;
use App\CoreModule\Helpers\StringsHelper;
use App\Model\NonPersistent\TemplateData\ProblemTemplateStateItem;
use App\Model\Persistent\Functionality\FilterFunctionality;
use App\Model\Persistent\Functionality\ProblemFinal\ProblemFinalFunctionality;
use App\Model\Persistent\Functionality\TestFunctionality;
use App\Model\Persistent\Functionality\TestVariantFunctionality;
use App\Model\Persistent\Functionality\UserFunctionality;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\CategoryRepository;
use App\Model\Persistent\Repository\FilterRepository;
use App\Model\Persistent\Repository\ProblemFinal\ProblemFinalRepository;
use App\Model\Persistent\Repository\ProblemRepository;
use App\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;
use App\Model\Persistent\Repository\ProblemTemplate\QuadraticEquationTemplateRepository;
use App\Model\Persistent\Repository\TemplateJsonDataRepository;
use App\Model\Persistent\Repository\TestRepository;
use App\TeacherModule\Plugins\QuadraticEquationPlugin;
use App\Services\Authorizator;
use App\TeacherModule\Services\ProblemGenerator;
use App\TeacherModule\Services\NewtonApiClient;
use App\TeacherModule\Services\PluginContainer;
use App\Services\ProblemTemplateSession;
use App\Services\Validator;
use jlawrence\eos\Parser;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DoctrinePresenter extends TeacherPresenter
{
    /**
     * @var ProblemRepository
     */
    protected $problemRepository;

    /**
     * @var ProblemFinalRepository
     */
    protected $problemFinalRepository;

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
     * @var QuadraticEquationTemplateRepository
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
     * @var ProblemGenerator
     */
    protected $generatorService;

    /**
     * @var QuadraticEquationPlugin
     */
    protected $quadraticEquationPlugin;

    /**
     * @var ProblemTemplateSession
     */
    protected $problemTemplateSession;

    /**
     * @var PluginContainer
     */
    protected $pluginContainer;

    /**
     * @var ProblemFinalFunctionality
     */
    protected $problemFinalFunctionality;

    /**
     * @var FilterRepository
     */
    protected $filterRepository;

    /**
     * @var FilterFunctionality
     */
    protected $filterFunctionality;

    /**
     * @var UserFunctionality
     */
    protected $userFunctionality;

    /**
     * DoctrinePresenter constructor.
     * @param Authorizator $authorizator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param ConstraintEntityManager $em
     * @param ProblemFinalRepository $problemFinalRepository
     * @param CategoryRepository $categoryRepository
     * @param TemplateJsonDataRepository $templateJsonDataRepository
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param QuadraticEquationTemplateRepository $quadraticEqTemplRepository
     * @param Validator $validator
     * @param LatexHelper $latexHelper
     * @param StringsHelper $stringsHelper
     * @param TestVariantFunctionality $testVariantFunctionality
     * @param TestFunctionality $testFunctionality
     * @param TestRepository $testRepository
     * @param IHelpModalFactory $sectionHelpModalFactory
     * @param Parser $parser
     * @param ProblemGenerator $generatorService
     * @param QuadraticEquationPlugin $quadraticEquationPlugin
     * @param ProblemTemplateSession $problemTemplateSession
     * @param PluginContainer $pluginContainer
     * @param ProblemFinalFunctionality $problemFinalFunctionality
     * @param ProblemRepository $problemRepository
     * @param FilterRepository $filterRepository
     * @param FilterFunctionality $filterFunctionality
     * @param UserFunctionality $userFunctionality
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        ConstraintEntityManager $em, ProblemFinalRepository $problemFinalRepository, CategoryRepository $categoryRepository,
        TemplateJsonDataRepository $templateJsonDataRepository,
        ProblemTemplateRepository $problemTemplateRepository, QuadraticEquationTemplateRepository $quadraticEqTemplRepository,
        Validator $validator,
        LatexHelper $latexHelper, StringsHelper $stringsHelper, TestVariantFunctionality $testVariantFunctionality,
        TestFunctionality $testFunctionality,
        TestRepository $testRepository,
        IHelpModalFactory $sectionHelpModalFactory,
        Parser $parser,
        ProblemGenerator $generatorService,
        QuadraticEquationPlugin $quadraticEquationPlugin,
        ProblemTemplateSession $problemTemplateSession,
        PluginContainer $pluginContainer,
        ProblemFinalFunctionality $problemFinalFunctionality,
        ProblemRepository $problemRepository,
        FilterRepository $filterRepository,
        FilterFunctionality $filterFunctionality,
        UserFunctionality $userFunctionality
    )
    {
        parent::__construct($authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory);
        $this->problemFinalRepository = $problemFinalRepository;
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
        $this->generatorService = $generatorService;
        $this->quadraticEquationPlugin = $quadraticEquationPlugin;
        $this->problemTemplateSession = $problemTemplateSession;
        $this->pluginContainer = $pluginContainer;
        $this->problemFinalFunctionality = $problemFinalFunctionality;
        $this->problemRepository = $problemRepository;
        $this->filterRepository = $filterRepository;
        $this->filterFunctionality = $filterFunctionality;
        $this->userFunctionality = $userFunctionality;
    }

    /**
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Nette\Utils\JsonException
     * @throws \App\Exceptions\GeneratorException
     */
    public function actionDefault()
    {
//        bdump(DateTime::from(''));
//        bdump(new DateTime());
//        bdump($this->parser::solve('e'));

        bdump('TESTING USER ENTITY');
        $data = ArrayHash::from([
            'username' => 'TEST USERNAME TEST USERNAME TEST USERNAME TEST USERNAME TEST USERNAME TEST USERNAME TEST USERNAME TEST USERNAME TEST USERNAME TEST USERNAME TEST USERNAME TEST USERNAME TEST USERNAME TEST USERNAME TEST USERNAME',
            'password' => 'TEST PASSWORD',
            'role' => 3,
            'groups' => [28],
            'firstName' => '',
            'lastName' => ''
        ]);
        $data->groups = [28];

        $user = $this->userFunctionality->create($data);

        $this->em->persist($user);


        bdump('TESTING FILTER ENTITY');
        $filter = $this->filterRepository->find(1);
        bdump($filter);

        bdump('Testing ProblemPlugin constructProblemFinalData');

        $problemTemplate = $this->problemRepository->find(38);
        bdump($problemTemplate);
        $problemTypeKeyLabel = $problemTemplate->getProblemType()->getKeyLabel();
        $problemPlugin = $this->pluginContainer->getPlugin($problemTypeKeyLabel);
        $linearEquationFinal = $problemPlugin->constructProblemFinal($problemTemplate, []);
        bdump($linearEquationFinal);

        $problemTemplate = $this->problemTemplateRepository->find(12);
        $problemTypeKeyLabel = $problemTemplate->getProblemType()->getKeyLabel();
        $problemPlugin = $this->pluginContainer->getPlugin($problemTypeKeyLabel);
        $quadraticEquationFinal = $problemPlugin->constructProblemFinal($problemTemplate, []);
        bdump($quadraticEquationFinal);

        $problemTemplate = $this->problemTemplateRepository->find(18);
        $problemTypeKeyLabel = $problemTemplate->getProblemType()->getKeyLabel();
        $problemPlugin = $this->pluginContainer->getPlugin($problemTypeKeyLabel);
        $linearEquationFinal = $problemPlugin->constructProblemFinal($problemTemplate, []);
        bdump($linearEquationFinal);

        $problemTemplate = $this->problemTemplateRepository->find(22);
        $problemTypeKeyLabel = $problemTemplate->getProblemType()->getKeyLabel();
        $problemPlugin = $this->pluginContainer->getPlugin($problemTypeKeyLabel);
        $linearEquationFinal = $problemPlugin->constructProblemFinal($problemTemplate, []);
        bdump($linearEquationFinal);

        bdump($this->latexHelper->postprocessProblemFinalBody('0 \big[ 5x + 15 \big] + 4x + 0 \frac{5x}{2} + 0*5 + 0 - 0 + 2'));

//        $this->em->flush();

        // ProblemTemplateState testing
        $problemTemplateStatusItem = new ProblemTemplateStateItem(1, true, true);
//        $this->problemTemplateSession->getProblemTemplate()->getState()->update($problemTemplateStatusItem);

        // EOS Parser testing
        bdump($this->parser::solve('(4*1 (3*1 + 3)) - (2 - 1) + 4 ((- 1))'));
        bdump($this->parser::solve('- 5'));

        // Get Discriminant A Tests
        $this->quadraticEquationPlugin->getDiscriminantA('p0 x^3 / p1 - (2 p0 + 5 + p1) x^2 - x / p3 - 5/2 + p2 / p3', 'x');
        $this->quadraticEquationPlugin->getDiscriminantA('p0 x^3 / p1 + x^2 - x/p3 - 5/2', 'x');
        $this->quadraticEquationPlugin->getDiscriminantA('x^2 - x/p3 - 5/2', 'x');
        $this->quadraticEquationPlugin->getDiscriminantA('- x^2 - x/p3 - 5/2', 'x');
        $this->quadraticEquationPlugin->getDiscriminantA('p0 x^3 / p1 - 2 p0 x^2 / p1 - x / p3 - 5/2 + p2 / p3', 'x');
        $this->quadraticEquationPlugin->getDiscriminantA('p0 x^3 / p1 - x^2 / p1 - x / p3 - 5/2 + p2 / p3', 'x');

        // Get Discriminant C Tests
        $this->quadraticEquationPlugin->getDiscriminantC('p0 x^3 / p1 - 2 p0 x^2 / p1 + x - 5/2 + p2 / p3', 'x');
        $this->quadraticEquationPlugin->getDiscriminantC('p0 x^3 / p1 - 2 p0 x^2 / p1 + p4 x / p5 - 5/2 + p2 / p3', 'x');
        $this->quadraticEquationPlugin->getDiscriminantC('p0 x^3 / p1 - 2 p0 x^2 / p1 - 5/2 + p2 / p3', 'x');
        $this->quadraticEquationPlugin->getDiscriminantC('p0 x^3 / p1 - 2 p0 x^2 / p1', 'x');
        $this->quadraticEquationPlugin->getDiscriminantC('p0 x^3 / p1 - 2 p0 x^2 / p1 + 5 x', 'x');

        // Get Discriminant B Tests
        $this->quadraticEquationPlugin->getDiscriminantB('p0 x^3 / p1 - 2 p0 x^2 / p1 - x / p3 - 5/2 + p2 / p3', 'x');
        $this->quadraticEquationPlugin->getDiscriminantB('p0 x^3 / p1 - 2 p0 x^2 / p1 + x / p3 - 5/2 + p2 / p3', 'x');
        $this->quadraticEquationPlugin->getDiscriminantB('p0 x^3 / p1 - 2 p0 x^2 / p1 - x / p3', 'x');
        $this->quadraticEquationPlugin->getDiscriminantB('p0 x^3 / p1 - 2 p0 x^2 / p1 - 2 p1 x / p3', 'x');
        $this->quadraticEquationPlugin->getDiscriminantB('p0 x^3 / p1 - 2 p0 x^2 / p1 - (2 p1 + 4 p2) x / p3', 'x');

        //bdump($this->stringsHelper::fillMultipliers('(1/15 p1 p2 - 1 3/ p2)/((5 / 2 p0 - 2 / p0 p2))'));
        //bdump($this->stringsHelper::firstOperator('(3/8 + 3/8 p0 - p2)'));

        $matches = Strings::matchAll('5 p0 x / ((x - 1) (x + 1)) - 10 p0 / ((x - 1) (x + 1)) - 3 / (x + 4) - 6 / ((x - 3) (x + 6))', '~([x\d\sp]*)\/\s*(\([\-\+\s\(\)\dx]*\))~');

        //bdump($matches);

        //bdump($this->stringsHelper::fillMultipliers('-p1 x^5 + (-3/5 - 3 p1) x^4 + (-9/5 + 5 p0 + 19 p1) x^3 + (27/5 + 5 p0 + 3 p1) x^2 + (9/5 - 120 p0 - 18 p1) x - 24/5 + 180 p0'));

        //bdump($this->parser::solve('(-3/5 - 3*0)'));

        //-3/5 + 5 p0 x / ((x - 1) (x + 1)) - 10 p0 / ((x - 1) (x + 1)) - p1 x - 6 / ((x - 3) (x + 6))

        //bdump($this->parser::solve('(- 1 + 5)/((5 + 2) (3*5 - 12*2))'));

        $allVarDividers = [];

        foreach ($matches as $match){
            $exploded = explode(') (',$this->stringsHelper::trim($match[2]));
            foreach ($exploded as $item){
                $itemTrimmed = $this->stringsHelper::trim($item);
                if(!isset($allVarDividers[$itemTrimmed])){
                    $allVarDividers[$itemTrimmed] = $itemTrimmed;
                }
            }
        }

        //bdump($allVarDividers);

        //bdump($this->parser::solve('1 - -5 + 1'));

        //bdump($this->generatorService->generateArrayUnique(1));

//        $templateJsons = $this->templateJsonDataRepository->findBy(['templateId' => 418]);
//
//        $firstJson = Json::decode($templateJsons[0]->getJsonData(), true);
//        //bdump($firstJson);
//
//        $secondJson = Json::decode($templateJsons[1]->getJsonData(), true);
//        //bdump($secondJson);
//
//        $intersect = array_uintersect($firstJson, $secondJson, static function($first, $second) {
//            return strcmp(serialize($first), serialize($second));
//        });
//
//        //bdump($intersect);

//        //bdump($this->parser::solve('5+4(1+2)+3+ln(e)'));
//        //bdump($this->parser::solve('0^2 - 4 * 1/2 log(3) * (1/2 0 - 1/5 log(100))'));
//
//        //bdump($this->parser::solve('2^(1 - 2) * 3^(-1 + 2)'));

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
//        //bdump($testVariant);
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
//        //bdump($errors);*/
//
//        $problemType = new ProblemType();
//        $problemType->setLabel("Funkce");

//        //bdump($this->validator->validateLinearEquation('55/15 x - 42/22 + 2/3 p2 - 43/20 p2 + 4 p3 + 15 p4', 'x'));
//        //bdump($this->latexHelper::parseLatex('$$ x^{2} $$'));
//        //bdump($this->latexHelper::parseLatex('$$ \frac{1}{2} \big( 2 x - 1 \big)^2 - \big( \frac{1}{2} \big( x + 1 \big) \big)^2 = 3 \big( \big( \frac{1}{2} x \big)^2 - \big( \frac{1}{<par min="2" max="10"/>} \big)^2 \big)^{2} $$'));
//
//        //bdump($this->stringsHelper::getLinearEquationRegExp('x'));
//        //bdump($this->stringsHelper::getQuadraticEquationRegExp('x'));
//        //bdump($this->templateJsonDataRepository->find(35));

        /*$entity = new ArithmeticSequenceTemplate();
        $errors = $this->validator->validate($entity);
        //bdump($errors);*/

        /*$entity = new ProblemCondition();
        $errors = $this->validator->validate($entity);
        //bdump($errors);*/

        /*$res = $this->categoryRepository->delete(500);
        //bdump($res);*/

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

        ////bdump($this->latexHelper::parseLatex('$$ \bigg \langle15 x + 5\bigg \rangle $$'));

    }
}