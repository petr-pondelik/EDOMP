<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 11:11
 */

namespace App\Model\Persistent\Functionality;

use App\Model\Persistent\Entity\Test;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\GroupRepository;
use App\Model\Persistent\Repository\LogoRepository;
use App\Model\Persistent\Repository\TestRepository;
use Nette\Utils\ArrayHash;

/**
 * Class TestFunctionality
 * @package App\Model\Persistent\Functionality
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
        $test->setLogo($this->logoRepository->find($data->logo));
        $test->setTerm($data->term);
        $test = $this->attachGroups($test, $data->groups);
        $test->setSchoolYear($data->schoolYear);
        $test->setTestNumber($data->testNumber);
        $test->setIntroductionText($data->introductionText);
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
}