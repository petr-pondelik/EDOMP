<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 16:40
 */

namespace App\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\TemplateJsonData;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;
use App\CoreModule\Model\Persistent\Repository\TemplateJsonDataRepository;

/**
 * Class TemplateJsonDataFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality
 */
class TemplateJsonDataFunctionality extends BaseFunctionality
{
    /**
     * @var ProblemTemplateRepository
     */
    protected $problemTemplateRepository;

    /**
     * @var ProblemConditionTypeRepository
     */
    protected $problemConditionTypeRepository;

    /**
     * TemplateJsonDataFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param TemplateJsonDataRepository $repository
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        TemplateJsonDataRepository $repository,
        ProblemTemplateRepository $problemTemplateRepository, ProblemConditionTypeRepository $problemConditionTypeRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->problemConditionTypeRepository = $problemConditionTypeRepository;
    }

    /**
     * @param iterable $data
     * @param bool $flush
     * @param int|null $templateId
     * @param int|null $conditionTypeId
     * @return BaseEntity|null
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function create(iterable $data, bool $flush = true, int $templateId = null, int $conditionTypeId = null): ?BaseEntity
    {
        if(!$templateId){
            $templateId = $this->problemTemplateRepository->getSequenceVal();
        }
        if( $jsonData = $this->repository->findOneBy([ 'templateId' => $templateId, 'problemConditionType' => $conditionTypeId ]) ){
            $jsonData->setJsonData($data->jsonData);
            $this->em->persist($jsonData);
            $this->em->flush();
            return $jsonData;
        }
        $jsonData = new TemplateJsonData();
        $jsonData->setJsonData($data->jsonData);
        $jsonData->setTemplateId($templateId);
        if($conditionTypeId){
            $jsonData->setProblemConditionType($this->problemConditionTypeRepository->find($conditionTypeId));
        }
        if(isset($data->created)){
            $jsonData->setCreated($data->created);
        }
        $this->em->persist($jsonData);
        if ($flush) {
            $this->em->flush();
        }
        return $jsonData;
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
     * @param int $templateId
     * @param bool $flush
     * @return bool
     * @throws \Exception
     */
    public function deleteByTemplate(int $templateId, bool $flush = true): bool
    {
        $toBeDeleted = $this->repository->findBy(['templateId' => $templateId]);
        foreach ($toBeDeleted as $item) {
            $this->em->remove($item);
        }
        if ($flush) {
            $this->em->flush();
        }
        return true;
    }
}