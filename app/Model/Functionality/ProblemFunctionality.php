<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.5.19
 * Time: 20:57
 */

namespace App\Model\Functionality;

use App\Model\Repository\ProblemRepository;
use App\Model\Repository\ProblemTestAssociationRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemFunctionality
 * @package App\Model\Functionality
 */
class ProblemFunctionality extends BaseFunctionality
{
    /**
     * @var ProblemTestAssociationRepository
     */
    protected $problemTestAssociationRepository;

    /**
     * ProblemFunctionality constructor.
     * @param EntityManager $entityManager
     * @param ProblemRepository $problemRepository
     * @param ProblemTestAssociationRepository $problemTestAssociationRepository
     */
    public function __construct
    (
        EntityManager $entityManager, ProblemRepository $problemRepository,
        ProblemTestAssociationRepository $problemTestAssociationRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $problemRepository;
        $this->problemTestAssociationRepository = $problemTestAssociationRepository;
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
        $problem = $this->repository->find($id);
        $problem->setSuccessRate($data->success_rate);
        $this->em->persist($problem);
        $this->em->flush();
        return $problem;
    }

    /**
     * @param int $id
     * @param bool $isTemplate
     * @throws \Exception
     */
    public function calculateSuccessRate(int $id, bool $isTemplate = false)
    {
        !$isTemplate ?
            $problems = $this->problemTestAssociationRepository->findBy(["problem.id" => $id]) :
            $problems = $this->problemTestAssociationRepository->findBy(["problemTemplate.id" => $id]);
        $cnt = 0;
        $ratingSum = 0;
        foreach ($problems as $problem){
            if(!empty($problem->getSuccessRate())){
                $cnt++;
                $ratingSum += $problem->getSuccessRate();
            }
        }
        if($cnt > 0){
            $this->update($id, ArrayHash::from([
                "success_rate" => ($ratingSum/$cnt)
            ]));
            return;
        }
        $this->update($id, ArrayHash::from([
            "success_rate" => null
        ]));
    }
}