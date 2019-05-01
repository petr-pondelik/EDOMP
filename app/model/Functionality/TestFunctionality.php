<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 11:11
 */

namespace App\Model\Functionality;

use App\Model\Entity\Problem;
use App\Model\Entity\ProblemFinal;
use App\Model\Entity\ProblemTemplate;
use App\Model\Entity\ProblemTestAssociation;
use App\Model\Entity\Test;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\LogoRepository;
use App\Model\Repository\TermRepository;
use App\Model\Repository\TestRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class TestFunctionality
 * @package App\Model\Functionality
 */
class TestFunctionality extends BaseFunctionality
{
    /**
     * @var LogoRepository
     */
    protected $logoRepository;

    /**
     * @var TermRepository
     */
    protected $termRepository;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * TestFunctionality constructor.
     * @param EntityManager $entityManager
     * @param TestRepository $repository
     * @param LogoRepository $logoRepository
     * @param TermRepository $termRepository
     * @param GroupRepository $groupRepository
     */
    public function __construct(
        EntityManager $entityManager, TestRepository $repository,
        LogoRepository $logoRepository, TermRepository $termRepository, GroupRepository $groupRepository)
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
        $this->logoRepository = $logoRepository;
        $this->termRepository = $termRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param ArrayHash $data
     * @return int
     * @throws \Exception
     */
    public function create(ArrayHash $data): int
    {
        $test = new Test();
        $test->setLogo($this->logoRepository->find($data->logo_id));
        $test->setTerm($this->termRepository->find($data->term_id));
        $test->setGroups([$this->groupRepository->find($data->group_id)]);
        $test->setSchoolYear($data->school_year);
        $test->setTestNumber($data->test_number);
        $test->setIntroductionText($data->introduction_text);
        $this->em->persist($test);
        $this->em->flush();
        return $test->getId();
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return Object
     */
    public function update(int $id, ArrayHash $data): ?Object
    {
        // TODO: Implement update() method.
        return null;
    }

    /**
     * @param Test $test
     * @param ProblemFinal $problem
     * @param string $variant
     * @param ProblemTemplate $template
     * @param bool $newPage
     * @throws \Exception
     */
    public function attachProblem(Test $test, ProblemFinal $problem, string $variant, ProblemTemplate $template = null, bool $newPage = false)
    {
        $association = new ProblemTestAssociation();
        $association->setTest($test);
        $association->setProblem($problem);
        $association->setVariant($variant);
        if($template !== null)
            $association->setProblemTemplate($template);
        $association->setNextPage($newPage);
        $this->em->persist($association);
        $test->addProblemAssociation($association);
        $this->em->persist($test);
        $this->em->flush();
    }
}