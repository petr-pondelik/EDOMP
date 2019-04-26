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
use App\Model\Entity\LinearEq;
use App\Model\Entity\Problem;
use App\Model\Entity\ProblemType;
use App\Model\Entity\SubCategory;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\LinearEqRepository;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Presenters\BasePresenter;
use Kdyby\Doctrine\EntityManager;

class DoctrinePresenter extends BasePresenter
{
    /**
     * @var ProblemRepository
     */
    protected $problemRepository;

    /**
     * @var LinearEqRepository
     */
    protected $linearEqRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct
    (
        EntityManager $em, ProblemRepository $problemRepository, CategoryRepository $categoryRepository, LinearEqRepository $linearEqRepository
    )
    {
        parent::__construct();
        $this->problemRepository = $problemRepository;
        $this->categoryRepository = $categoryRepository;
        $this->linearEqRepository = $linearEqRepository;
        //$this->difficultyRepository = $difficultyRepository;
        //$this->subCategoryRepository = $subCategoryRepository;
        $this->em = $em;
    }

    public function actionDefault()
    {
        $category = new Category();
        $category->setLabel("Lineární rovnice");
        $this->em->persist($category);
        $this->em->flush();

        $subCategory = new SubCategory();
        $subCategory->setLabel("1.1 Lineární rovnice");
        $category = $this->categoryRepository->find(1);
        $subCategory->setCategory($category);
        $this->em->persist($subCategory);
        $this->em->flush();

        $problemType = new ProblemType();
        $problemType->setLabel("Lineární rovnice");
        $this->em->persist($problemType);
        $this->em->flush();

        $difficulty = new Difficulty();
        $difficulty->setLabel("Lehká");
        $this->em->persist($difficulty);
        $this->em->flush();

        $linearEq = new LinearEq();
        bdump($linearEq instanceof LinearEq);
        $linearEq->setDifficulty($difficulty);
        $linearEq->setSubCategory($subCategory);
        $linearEq->setProblemType($problemType);
        $linearEq->setBody("15x + 10 x + 20 = 0");
        $linearEq->setVariable("x");
        $this->em->persist($linearEq);
        $this->em->flush();

        //$this->em->safePersist($problem);
        //$this->em->flush();

        bdump($this->problemRepository->findAll());
        bdump($this->linearEqRepository->findAll());
    }
}