<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 20:31
 */

namespace App\Model\Functionality;

use App\Model\Entity\SequenceInfo;
use App\Model\Repository\SequenceInfoRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class SequenceInfoFunctionality
 * @package App\Model\Functionality
 */
class SequenceInfoFunctionality extends BaseFunctionality
{

    public function __construct
    (
        EntityManager $entityManager,
        SequenceInfoRepository $repository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
    }

    /**
     * @param ArrayHash $data
     * @return void
     * @throws \Exception
     */
    public function create(ArrayHash $data): void
    {
        $seqInfo = new SequenceInfo($data->problemTemplateSeqVal);
        $this->em->persist($seqInfo);
        $this->em->flush();
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return Object
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data): ?Object
    {
        $seqInfo = $this->repository->find($id);
        $seqInfo->setProblemTemplateSeqVal($data->problemTemplateSeqVal);
        $this->em->persist($seqInfo);
        $this->em->flush();
        return $seqInfo;
    }

    /**
     * @param ArrayHash $data
     * @throws \Exception
     */
    public function storeInfo(ArrayHash $data): void
    {
        if($this->repository->count([])){
            $this->update(1, $data);
            return;
        }
        $this->create($data);
    }
}