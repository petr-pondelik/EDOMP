<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.5.19
 * Time: 20:57
 */

namespace App\Model\Persistent\Functionality;

use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\ProblemRepository;
use App\Model\Persistent\Repository\ProblemFinalTestVariantAssociationRepository;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemFunctionality
 * @package App\Model\Persistent\Functionality
 */
class ProblemFunctionality extends BaseFunctionality
{
    /**
     * @var ProblemFinalTestVariantAssociationRepository
     */
    protected $problemFinalTestVariantAssociationRepository;

    /**
     * ProblemFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param ProblemRepository $problemRepository
     * @param ProblemFinalTestVariantAssociationRepository $problemFinalTestVariantAssociationRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager, ProblemRepository $problemRepository,
        ProblemFinalTestVariantAssociationRepository $problemFinalTestVariantAssociationRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $problemRepository;
        $this->problemFinalTestVariantAssociationRepository = $problemFinalTestVariantAssociationRepository;
    }

    /**
     * @param ArrayHash $data
     * @param bool $flush
     * @return BaseEntity|null
     */
    public function create(ArrayHash $data, bool $flush = true): ?BaseEntity
    {
        return null;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws EntityNotFoundException
     * @throws \App\Exceptions\EntityException
     */
    public function update(int $id, ArrayHash $data, bool $flush = true): ?BaseEntity
    {
        $problem = $this->repository->find($id);
        if(!$problem){
            throw new EntityNotFoundException('Entity for update not found.');
        }
        $problem->setSuccessRate($data->success_rate);
        $this->em->persist($problem);
        if ($flush) {
            $this->em->flush();
        }
        return $problem;
    }

    /**
     * @param int $id
     * @param bool $isTemplate
     * @param bool $flush
     * @throws EntityNotFoundException
     * @throws \App\Exceptions\EntityException
     */
    public function calculateSuccessRate(int $id, bool $isTemplate = false, bool $flush = true): void
    {
        !$isTemplate ?
            $associations = $this->problemFinalTestVariantAssociationRepository->findBy(['problemFinal.id' => $id]) :
            $associations = $this->problemFinalTestVariantAssociationRepository->findBy(['problemTemplate.id' => $id]);
        $cnt = 0;
        $ratingSum = 0;
        foreach ($associations as $association){
            if(!empty($association->getSuccessRate())){
                $cnt++;
                $ratingSum += $association->getSuccessRate();
            }
        }
        if($cnt > 0){
            $this->update($id, ArrayHash::from([ 'success_rate' => $ratingSum / $cnt ]), $flush);
            return;
        }
        $this->update($id, ArrayHash::from([ 'success_rate' => null ]), $flush);
    }
}