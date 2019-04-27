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

    }
}