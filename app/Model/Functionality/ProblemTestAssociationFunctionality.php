<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.5.19
 * Time: 21:01
 */

namespace App\Model\Functionality;

use App\Model\Repository\ProblemTestAssociationRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemTestAssociationFunctionality
 * @package App\Model\Functionality
 */
class ProblemTestAssociationFunctionality extends BaseFunctionality
{

    /**
     * ProblemTestAssociationFunctionality constructor.
     * @param EntityManager $entityManager
     * @param ProblemTestAssociationRepository $repository
     */
    public function __construct(EntityManager $entityManager, ProblemTestAssociationRepository $repository)
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
            "problem.id" => $id,
            "test.id" => $data->test_id
        ]);
        if(empty($data->success_rate))
            $data->success_rate = null;
        $association->setSuccessRate($data->success_rate);
        $this->em->persist($association);
        $this->em->flush();
        return $association;
    }
}