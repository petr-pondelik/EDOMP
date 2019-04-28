<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 16:40
 */

namespace App\Model\Functionality;

use App\Model\Entity\TemplateJsonData;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use Kdyby\Doctrine\EntityManager;
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
     * @param EntityManager $entityManager
     * @param TemplateJsonDataRepository $repository
     * @param ProblemTemplateRepository $problemTemplateRepository
     */
    public function __construct
    (
        EntityManager $entityManager,
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
     * @return void
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function create(ArrayHash $data, int $templateId = null): void
    {
        if(!$templateId)
            $templateId = $this->problemTemplateRepository->getLastId() + 1;

        if($jsonData = $this->repository->findOneBy([ "templateId" => $templateId ])){
            $jsonData->setJsonData($data->jsonData);
            $this->em->persist($jsonData);
            $this->em->flush();
            return;
        }

        $jsonData = new TemplateJsonData();
        $jsonData->setJsonData($data->jsonData);
        $jsonData->setProblemId($templateId);
        $this->em->persist($jsonData);
        $this->em->flush();
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     */
    public function update(int $id, ArrayHash $data): void
    {
        // TODO: Implement update() method.
    }
}