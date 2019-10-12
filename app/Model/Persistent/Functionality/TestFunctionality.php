<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 11:11
 */

namespace App\Model\Persistent\Functionality;

use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Entity\Test;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\GroupRepository;
use App\Model\Persistent\Repository\LogoRepository;
use App\Model\Persistent\Repository\TestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
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
     * @var ProblemFinalTestVariantAssociationFunctionality
     */
    protected $problemFinalTestVariantAssociationFunctionality;

    /**
     * @var ProblemFunctionality
     */
    protected $problemFunctionality;

    /**
     * TestFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param TestRepository $repository
     * @param LogoRepository $logoRepository
     * @param GroupRepository $groupRepository
     * @param ProblemFinalTestVariantAssociationFunctionality $problemFinalTestVariantAssociationFunctionality
     * @param ProblemFunctionality $problemFunctionality
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager, TestRepository $repository,
        LogoRepository $logoRepository, GroupRepository $groupRepository,
        ProblemFinalTestVariantAssociationFunctionality $problemFinalTestVariantAssociationFunctionality,
        ProblemFunctionality $problemFunctionality
    )
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
        $this->logoRepository = $logoRepository;
        $this->groupRepository = $groupRepository;
        $this->problemFinalTestVariantAssociationFunctionality = $problemFinalTestVariantAssociationFunctionality;
        $this->problemFunctionality = $problemFunctionality;
    }

    /**
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        $test = new Test();

        $test->setLogo($this->logoRepository->find($data->logo));
        $test->setTerm($data->term);
        $test = $this->attachGroups($test, $data->groups);
        $test->setSchoolYear($data->schoolYear);
        $test->setTestNumber($data->testNumber);
        $test->setIntroductionText($data->introductionText);
        $test->setVariantsCnt($data->variantsCnt);
        $test->setProblemsPerVariant($data->problemsPerVariant);

        $this->em->persist($test);

        if ($flush) {
            $this->em->flush();
        }

        return $test;
    }

    /**
     * @param int $id
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws EntityNotFoundException
     * @throws \App\Exceptions\EntityException
     */
    public function update(int $id, iterable $data, bool $flush = true): ?BaseEntity
    {
        bdump('TEST FUNCTIONALITY UPDATE');
        bdump($data);

        $entity = $this->repository->find($id);
        if (!$entity) {
            throw new EntityNotFoundException('Entity for update not found.');
        }

        if ($data->updateBasics) {
            $entity->setLogo($this->logoRepository->find($data->logo));
            $entity->setTerm($data->testTerm);
            $entity->setSchoolYear($data->schoolYear);
            $entity->setTestNumber($data->testNumber);
            $entity->setIntroductionText($data->introductionText);
            $entity = $this->updateGroups($entity, ArrayHash::from($data->groups));
        }

        if ($data->updateStatistics) {
            $this->updateStatistics($entity, $data, false);
        }

        $this->em->persist($entity);

        if ($flush) {
            $this->em->flush();
        }

        return $entity;
    }

    /**
     * @param Test $entity
     * @param iterable $data
     * @param bool $flush
     * @return Test|null
     * @throws EntityNotFoundException
     * @throws \App\Exceptions\EntityException
     */
    public function updateStatistics(Test $entity, iterable $data, bool $flush = true): ?Test
    {
        // Get test variants
        $testVariants = $entity->getTestVariants()->getValues();

        // Update success rates for in ProblemFinalTestVariantAssociations
        for ($i = 0; $i < $entity->getVariantsCnt(); $i++) {
            for ($j = 0; $j < $entity->getProblemsPerVariant(); $j++) {
                $this->problemFinalTestVariantAssociationFunctionality->update(
                    $data->{'problemFinalId' . $i . $j},
                    ArrayHash::from([
                        'testVariant' => $testVariants[$i]->getId(),
                        'successRate' => $data->{'successRate' . $i . $j}
                    ]),
                    false
                );
            }
        }

        // Recalculate success rates for associated ProblemFinals and ProblemTemplates entities
        for ($i = 0; $i < $entity->getVariantsCnt(); $i++) {
            for ($j = 0; $j < $entity->getProblemsPerVariant(); $j++) {
                $this->problemFunctionality->calculateSuccessRate($data->{'problemFinalId' . $i . $j}, false, false);
                if (!empty($data->{'problemTemplateId' . $i . $j})) {
                    $this->problemFunctionality->calculateSuccessRate($data->{'problemTemplateId' . $i . $j}, true, false);
                }
            }
        }

        if ($flush) {
            $this->em->flush();
        }

        return $entity;
    }

    /**
     * @param Test $entity
     * @param iterable $groups
     * @return Test
     */
    public function updateGroups(Test $entity, iterable $groups): Test
    {
        $entity->setGroups(new ArrayCollection());
        return $this->attachGroups($entity, $groups);
    }

    /**
     * @param Test $entity
     * @param iterable $groups
     * @return Test
     */
    public function attachGroups(Test $entity, iterable $groups): Test
    {
        foreach ($groups as $groupId) {
            $entity->addGroup($this->groupRepository->find($groupId));
        }
        return $entity;
    }

    /**
     * @param int $id
     * @return Test
     * @throws EntityNotFoundException
     * @throws \App\Exceptions\EntityException
     */
    public function close(int $id): Test
    {
        $entity = $this->repository->find($id);
        if (!$entity) {
            throw new EntityNotFoundException('Test for closing was not found.');
        }
        $entity->setIsClosed(true);
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }
}