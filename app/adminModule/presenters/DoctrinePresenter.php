<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 12:34
 */

namespace App\AdminModule\Presenters;


use App\Model\Entity\Category;
use App\Model\Entity\ProblemFinal;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\LinearEqRepository;
use App\Model\Repository\ProblemFinalRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\QuadraticEqTemplRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use App\Presenters\BasePresenter;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\Json;

class DoctrinePresenter extends BasePresenter
{
    /**
     * @var ProblemFinalRepository
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

    public function __construct
    (
        EntityManager $em, ProblemFinalRepository $problemRepository, CategoryRepository $categoryRepository,
        LinearEqRepository $linearEqRepository, TemplateJsonDataRepository $templateJsonDataRepository,
        ProblemTemplateRepository $problemTemplateRepository, QuadraticEqTemplRepository $quadraticEqTemplRepository
    )
    {
        parent::__construct();
        $this->problemRepository = $problemRepository;
        $this->categoryRepository = $categoryRepository;
        $this->linearEqRepository = $linearEqRepository;
        $this->templateJsonDataRepository = $templateJsonDataRepository;
        //$this->difficultyRepository = $difficultyRepository;
        //$this->subCategoryRepository = $subCategoryRepository;
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->quadraticEqTemplRepository = $quadraticEqTemplRepository;
        $this->em = $em;
    }

    public function actionDefault()
    {
        //bdump(Json::decode($this->problemTemplateRepository->findOneBy(["id" => 4])->getMatches()));
        //$this->em->getConnection()->get
        //bdump($this->quadraticEqTemplRepository->getLastId());
        $category = new Category();
        $category->setLabel("TEXT");
        $this->em->persist($category);
        $this->em->flush();
        bdump($category->getId());
    }
}