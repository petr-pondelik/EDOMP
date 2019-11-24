<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.5.19
 * Time: 21:01
 */

namespace App\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\ProblemFinalTestVariantAssociationRepository;
use Nette\Utils\Strings;

/**
 * Class ProblemFinalTestVariantAssociationFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality
 */
class ProblemFinalTestVariantAssociationFunctionality extends BaseFunctionality
{
    /**
     * ProblemTestAssociationFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param ProblemFinalTestVariantAssociationRepository $repository
     */
    public function __construct(ConstraintEntityManager $entityManager, ProblemFinalTestVariantAssociationRepository $repository)
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
    }

    /**
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        return null;
    }

    /**
     * @param int $id
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function update(int $id, iterable $data, bool $flush = true): ?BaseEntity
    {
        $association = $this->repository->findOneBy([
            'problemFinal.id' => $id,
            'testVariant.id' => $data->testVariant
        ]);
        if(empty($data->successRate)){
            $association->setSuccessRate(null);
        }
        else{
            $association->setSuccessRate(Strings::replace($data->successRate, '~,~', '.'));
        }
        $this->em->persist($association);
        if($flush) {
            $this->em->flush();
        }
        return $association;
    }
}