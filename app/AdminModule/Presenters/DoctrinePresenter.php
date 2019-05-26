<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 12:34
 */

namespace App\AdminModule\Presenters;


use App\Model\Entity\Category;
use App\Model\Entity\Difficulty;
use App\Model\Entity\Group;
use App\Model\Entity\ProblemFinal;
use App\Model\Entity\ProblemType;
use App\Model\Entity\QuadraticEqTempl;
use App\Model\Entity\SubCategory;
use App\Model\Entity\Test;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemFinalRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\QuadraticEqTemplRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use App\Presenters\BasePresenter;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\DateTime;
use Nette\Utils\Json;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DoctrinePresenter extends BasePresenter
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
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * DoctrinePresenter constructor.
     * @param EntityManager $em
     * @param ProblemFinalRepository $problemRepository
     * @param CategoryRepository $categoryRepository
     * @param TemplateJsonDataRepository $templateJsonDataRepository
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param QuadraticEqTemplRepository $quadraticEqTemplRepository
     * @param ValidatorInterface $validator
     */
    public function __construct
    (
        EntityManager $em, ProblemFinalRepository $problemRepository, CategoryRepository $categoryRepository,
        TemplateJsonDataRepository $templateJsonDataRepository,
        ProblemTemplateRepository $problemTemplateRepository, QuadraticEqTemplRepository $quadraticEqTemplRepository,
        ValidatorInterface $validator
    )
    {
        parent::__construct();
        $this->problemRepository = $problemRepository;
        $this->categoryRepository = $categoryRepository;
        $this->templateJsonDataRepository = $templateJsonDataRepository;
        //$this->difficultyRepository = $difficultyRepository;
        //$this->subCategoryRepository = $subCategoryRepository;
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->quadraticEqTemplRepository = $quadraticEqTemplRepository;
        $this->em = $em;
        $this->validator = $validator;
    }

    public function actionDefault()
    {
        $category = new Category();
        $errors = $this->validator->validate($category);
        bdump($errors);

        $difficulty = new Difficulty();
        $errors = $this->validator->validate($difficulty);
        bdump($errors);

        $group = new Group();
        $errors = $this->validator->validate($group);
        bdump($errors);

        $problemFinal = new ProblemFinal();
        $problemFinal->setBody("$$ 15 x + 20 = 0 $$");
        $problemFinal->setDifficulty($difficulty);
        $problemFinal->setVariable("x");
        $errors = $this->validator->validate($problemFinal);
        bdump($errors);

        $test = new Test();
        $test->setTestNumber(-10);
        $test->setSchoolYear("2018-19");
        $errors = $this->validator->validate($test);
        bdump($errors);

        /*$this->em->persist($category);
        $this->em->flush();*/

    }
}