<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.5.19
 * Time: 21:01
 */

namespace App\Model\Persistent\Functionality;

use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\ProblemFinalTestVariantAssociationRepository;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class ProblemTestAssociationFunctionality
 * @package App\Model\Persistent\Functionality
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
     * @param ArrayHash $data
     * @return Object|null
     */
    public function create(ArrayHash $data): ?Object
    {
        return null;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return Object
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data): ?Object
    {
        $association = $this->repository->findOneBy([
            'problemFinal.id' => $id,
            'testVariant.id' => $data->test_variants_id
        ]);
        if(empty($data->success_rate)){
            $association->setSuccessRate(null);
        }
        else{
            $association->setSuccessRate(Strings::replace($data->success_rate, '~,~', '.'));
        }
        $this->em->persist($association);
        $this->em->flush();
        return $association;
    }
}