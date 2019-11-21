<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 18.7.19
 * Time: 17:56
 */

namespace App\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\CoreModule\Model\Persistent\Entity\ProblemFinalTestVariantAssociation;
use App\CoreModule\Model\Persistent\Entity\TestVariant;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\TestVariantRepository;

/**
 * Class TestVariantFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality
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
     * @throws \App\CoreModule\Exceptions\EntityException
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
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function attachProblem(TestVariant $testVariant, ProblemFinal $problemFinal, bool $newPage = false): TestVariant
    {
        bdump('ATTACH PROBLEM');
        bdump($problemFinal);
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
     * @throws \App\CoreModule\Exceptions\EntityException
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