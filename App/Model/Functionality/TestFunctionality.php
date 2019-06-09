<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 11:11
 */

namespace App\Model\Functionality;

use App\Model\Entity\ProblemFinal;
use App\Model\Entity\ProblemTemplate;
use App\Model\Entity\ProblemTestAssociation;
use App\Model\Entity\Test;
use App\Model\Manager\ConstraintEntityManager;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\LogoRepository;
use App\Model\Repository\TestRepository;
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
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * TestFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param TestRepository $repository
     * @param LogoRepository $logoRepository
     * @param GroupRepository $groupRepository
     */
    public function __construct(
        ConstraintEntityManager $entityManager, TestRepository $repository,
        LogoRepository $logoRepository, GroupRepository $groupRepository)
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
        $this->logoRepository = $logoRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param ArrayHash $data
     * @return Object|null
     * @throws \Exception
     */
    public function create(ArrayHash $data): ?Object
    {
        $test = new Test();
        $test->setLogo($this->logoRepository->find($data->logo_id));
        $test->setTerm($data->term);
        $test = $this->attachGroups($test, $data->groups);
        $test->setSchoolYear($data->school_year);
        $test->setTestNumber($data->test_number);
        $test->setIntroductionText($data->introduction_text);
        if(isset($data->created)){
            $test->setCreated($data->created);
        }
        $this->em->persist($test);
        return $test;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return Object
     */
    public function update(int $id, ArrayHash $data): ?Object
    {
        return null;
    }

    /**
     * @param Test $test
     * @param ArrayHash $groups
     * @return Test
     */
    public function attachGroups(Test $test, ArrayHash $groups): Test
    {
        foreach ($groups as $groupId){
            $test->addGroup($this->groupRepository->find($groupId));
        }
        return $test;
    }

    /**
     * @param Test $test
     * @param ProblemFinal $problem
     * @param string $variant
     * @param ProblemTemplate $template
     * @param bool $newPage
     * @throws \Exception
     */
    public function attachProblem(Test $test, ProblemFinal $problem, string $variant, ProblemTemplate $template = null, bool $newPage = false): void
    {
        $association = new ProblemTestAssociation();
        $association->setTest($test);
        $association->setProblem($problem);
        $association->setVariant($variant);
        if($template !== null){
            $association->setProblemTemplate($template);
        }
        $association->setNextPage($newPage);
        $this->em->persist($association);
        $test->addProblemAssociation($association);
        $this->em->persist($test);
    }
}