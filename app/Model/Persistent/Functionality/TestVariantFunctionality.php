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
use App\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\Model\Persistent\Entity\TestVariant;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\TestVariantRepository;
use Nette\Utils\ArrayHash;

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
     * @param ArrayHash $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     */
    public function create(ArrayHash $data, bool $flush = true): ?BaseEntity
    {
        $entity = new TestVariant();
        $entity->setLabel($data->variantLabel);
        $entity->setTest($data->test);
        $this->em->persist($entity);
        return $entity;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @param bool $flush
     * @return BaseEntity|null
     */
    public function update(int $id, ArrayHash $data, bool $flush = true): ?BaseEntity
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
}