<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 18.7.19
 * Time: 17:56
 */

namespace App\Model\Functionality;

use App\Model\Entity\ProblemFinal;
use App\Model\Entity\ProblemFinalTestVariantAssociation;
use App\Model\Entity\ProblemTemplate;
use App\Model\Entity\TestVariant;
use App\Model\Manager\ConstraintEntityManager;
use App\Model\Repository\TestVariantRepository;
use Nette\Utils\ArrayHash;

/**
 * Class TestVariantFunctionality
 * @package App\Model\Functionality
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
     * @return Object|null
     * @throws \Exception
     */
    public function create(ArrayHash $data): ?Object
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
     * @return Object
     */
    public function update(int $id, ArrayHash $data): ?Object
    {
        return null;
    }

    /**
     * @param TestVariant $testVariant
     * @param ProblemFinal $problemFinal
     * @param ProblemTemplate|null $problemTemplate
     * @param bool $newPage
     * @return TestVariant
     * @throws \App\Exceptions\EntityException
     */
    public function attachProblem(TestVariant $testVariant, ProblemFinal $problemFinal, ProblemTemplate $problemTemplate = null, bool $newPage = false): TestVariant
    {
        bdump('ATTACH PROBLEM');
        bdump($problemTemplate);
        $association = new ProblemFinalTestVariantAssociation();
        $association->setTestVariant($testVariant);
        $association->setProblemFinal($problemFinal);
        if($problemTemplate){
            $association->setProblemTemplate($problemTemplate);
        }
        $association->setNextPage($newPage);
        $this->em->persist($association);
        $testVariant->addProblemFinalAssociation($association);
        $this->em->persist($testVariant);
        return $testVariant;
    }
}