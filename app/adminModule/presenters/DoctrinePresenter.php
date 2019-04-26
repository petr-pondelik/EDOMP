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
use App\Model\Entity\Problem;
use App\Model\Repository\DifficultyRepository;
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
     * @var EntityManager
     */
    protected $em;

    public function __construct
    (
        EntityManager $em, ProblemRepository $problemRepository
    )
    {
        parent::__construct();
        $this->problemRepository = $problemRepository;
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

        //bdump($this->problemRepository->findAll());

        /*$difficulty = new Difficulty();
        $difficulty->setLabel("SSS");
        $this->em->persist($difficulty);
        $this->em->flush();

        bdump($difficulty);

        $problem = new Problem();
        $problem->setBody("$$ x + 15 = 10 $$");
        $problem->setDifficulty($difficulty);
        $this->em->persist($problem);
        $this->em->flush();

        bdump($this->problemRepository->findAll());
        bdump($this->difficultyRepository->findAll()[0]->getProblems()[0]);

        bdump($problem);*/
    }
}