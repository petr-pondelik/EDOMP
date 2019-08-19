<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 16:40
 */

namespace App\Model\Functionality;

use App\Model\Entity\TemplateJsonData;
use App\Model\Manager\ConstraintEntityManager;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use Nette\Utils\ArrayHash;

/**
 * Class TemplateJsonDataFunctionality
 * @package App\Model\Functionality
 */
class TemplateJsonDataFunctionality extends BaseFunctionality
{
    /**
     * @var ProblemTemplateRepository
     */
    protected $problemTemplateRepository;

    /**
     * TemplateJsonDataFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param TemplateJsonDataRepository $repository
     * @param ProblemTemplateRepository $problemTemplateRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        TemplateJsonDataRepository $repository, ProblemTemplateRepository $problemTemplateRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
        $this->problemTemplateRepository = $problemTemplateRepository;
    }

    /**
     * @param ArrayHash $data
     * @param int|null $templateId
     * @param bool $isValidation
     * @return Object|null
     * @throws \App\Exceptions\EntityException
     * @throws \Exception
     */
    public function create(ArrayHash $data, int $templateId = null, bool $isValidation = false): ?Object
    {
        if(!$templateId){
            $templateId = $this->problemTemplateRepository->getSequenceVal();
        }
        if( $jsonData = $this->repository->findOneBy([ 'templateId' => $templateId, 'isValidation' => $isValidation ]) ){
            $jsonData->setJsonData($data->jsonData);
            $this->em->persist($jsonData);
            $this->em->flush();
            return $jsonData;
        }
        $jsonData = new TemplateJsonData();
        $jsonData->setJsonData($data->jsonData);
        $jsonData->setTemplateId($templateId);
        if(isset($data->created)){
            $jsonData->setCreated($data->created);
        }
        $jsonData->setIsValidation($isValidation);
        $this->em->persist($jsonData);
        $this->em->flush();
        return $jsonData;
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
     * @param int $templateId
     * @return bool
     * @throws \Exception
     */
    public function deleteByTemplate(int $templateId): bool
    {
        $toBeDeleted = $this->repository->findBy(['templateId' => $templateId]);
        foreach ($toBeDeleted as $item) {
            $this->em->remove($item);
        }
        $this->em->flush();
        return true;
    }
}