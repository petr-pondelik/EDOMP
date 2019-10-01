<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 18.7.19
 * Time: 17:56
 */

namespace App\Model\Persistent\Functionality;

use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\Model\Persistent\Entity\ProblemFinalTestVariantAssociation;
use App\Model\Persistent\Entity\TestVariant;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\TestVariantRepository;

/**
 * Class TestVariantFunctionality
 * @package App\Model\Persistent\Functionality
 */
class TestVariantFunctionality extends BaseFunctionality
{
    /**
     * TestVariantFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param TestVariantRepository $testVariantRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager, TestVariantRepository $testVariantRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $testVariantRepository;
    }

    /**
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        $entity = new TestVariant();
        $entity->setLabel($data->variantLabel);
        $entity->setTest($data->test);
        $this->em->persist($entity);
        return $entity;
    }

    /**
     * @param int $id
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     */
    public function update(int $id, iterable $data, bool $flush = true): ?BaseEntity
    {
        return null;
    }

    /**
     * @param TestVariant $testVariant
     * @param ProblemFinal $problemFinal
     * @param bool $newPage
     * @return TestVariant
     * @throws \App\Exceptions\EntityException
     */
    public function attachProblem(TestVariant $testVariant, ProblemFinal $problemFinal, bool $newPage = false): TestVariant
    {
        $association = new ProblemFinalTestVariantAssociation();
        $association->setTestVariant($testVariant);
        $association->setProblemFinal($problemFinal);
        $association->setNextPage($newPage);
        $this->em->persist($association);
        $testVariant->addProblemFinalAssociation($association);
        $this->em->persist($testVariant);
        return $testVariant;
    }

    /**
     * @param TestVariant $testVariant
     * @param ProblemFinalTestVariantAssociation $original
     * @return TestVariant
     * @throws \App\Exceptions\EntityException
     */
    public function attachAssociationFromOriginal(TestVariant $testVariant, ProblemFinalTestVariantAssociation $original): TestVariant
    {
        bdump('ATTACH ASSOCIATION FROM ORIGINAL');
        $association = new ProblemFinalTestVariantAssociation();
        $association->setTestVariant($testVariant);
        $association->setProblemFinal($original->getProblemFinal());
        $association->setNextPage($original->isNextPage());
        $this->em->persist($association);
        $testVariant->addProblemFinalAssociation($association);
        $this->em->persist($testVariant);
        return $testVariant;
    }
}