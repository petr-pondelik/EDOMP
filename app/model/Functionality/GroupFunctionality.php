<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 23:13
 */

namespace App\Model\Functionality;

use App\Model\Entity\Group;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\SuperGroupRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class GroupFunctionality
 * @package App\Model\Functionality
 */
class GroupFunctionality extends BaseFunctionality
{
    /**
     * @var SuperGroupRepository
     */
    protected $superGroupRepository;

    /**
     * GroupFunctionality constructor.
     * @param EntityManager $entityManager
     * @param GroupRepository $repository
     * @param SuperGroupRepository $superGroupRepository
     */
    public function __construct(EntityManager $entityManager, GroupRepository $repository, SuperGroupRepository $superGroupRepository)
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
        $this->superGroupRepository = $superGroupRepository;
    }

    /**
     * @param ArrayHash $data
     * @return int
     * @throws \Exception
     */
    public function create(ArrayHash $data): int
    {
        $group = new Group();
        $group->setLabel($data->label);
        $group->setSuperGroup($this->superGroupRepository->find($data->super_group_id));
        $this->em->persist($group);
        $this->em->flush();
        return $group->getId();
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return Object
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data): ?Object
    {
        $group = $this->repository->find($id);
        if(isset($data->label))
            $group->setLabel($data->label);
        if(isset($data->super_group_id))
            $group->setSuperGroup($this->superGroupRepository->find($data->super_group_id));
        $this->em->persist($group);
        $this->em->flush();
        return $group;
    }
}