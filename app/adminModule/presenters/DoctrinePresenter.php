<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 12:34
 */

namespace App\AdminModule\Presenters;


use App\Model\Repository\CategoryRepository;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\LinearEqRepository;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use App\Presenters\BasePresenter;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\Json;

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
     * @var TemplateJsonDataRepository
     */
    protected $templateJsonDataRepository;

    /**
     * @var
     */
    protected $problemTemplateRepository;

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct
    (
        EntityManager $em, ProblemRepository $problemRepository, CategoryRepository $categoryRepository,
        LinearEqRepository $linearEqRepository, TemplateJsonDataRepository $templateJsonDataRepository,
        ProblemTemplateRepository $problemTemplateRepository
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
        $this->em = $em;
    }

    public function actionDefault()
    {
        //bdump(Json::decode($this->problemTemplateRepository->findOneBy(["id" => 4])->getMatches()));
    }
}