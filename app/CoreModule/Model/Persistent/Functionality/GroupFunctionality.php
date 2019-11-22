<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 23:13
 */

namespace App\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\Group;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use App\CoreModule\Model\Persistent\Repository\GroupRepository;
use App\CoreModule\Model\Persistent\Repository\SuperGroupRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class GroupFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality
 */
class GroupFunctionality extends BaseFunctionality
{
    /**
     * @var SuperGroupRepository
     */
    protected $superGroupRepository;

    /**
     * @var ThemeRepository
     */
    protected $themeRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * GroupFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param GroupRepository $repository
     * @param SuperGroupRepository $superGroupRepository
     * @param ThemeRepository $themeRepository
     * @param UserRepository $userRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager, GroupRepository $repository,
        SuperGroupRepository $superGroupRepository, ThemeRepository $themeRepository,
        UserRepository $userRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
        $this->superGroupRepository = $superGroupRepository;
        $this->themeRepository = $themeRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        $group = new Group();
        $group->setLabel($data->label);
        $group->setSuperGroup($this->superGroupRepository->find($data->superGroup));
        if(isset($data->user_id)){
            $group->setCreatedBy($this->userRepository->find($data->user_id));
        }
        if(isset($data->created)){
            $group->setCreated($data->created);
        }
        $this->em->persist($group);
        if($flush){
            $this->em->flush();
        }
        return $group;
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
        $group = $this->repository->find($id);
        if(isset($data->label)){
            $group->setLabel($data->label);
        }
        if(isset($data->superGroup)){
            $group->setSuperGroup($this->superGroupRepository->find($data->superGroup));
        }
        $this->em->persist($group);
        if($flush){
            $this->em->flush();
        }
        return $group;
    }

    /**
     * @param int $id
     * @param array $themes
     * @param bool $flush
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function updatePermissions(int $id, array $themes, bool $flush = true): void
    {
        $group = $this->repository->find($id);
        $group->setThemes(new ArrayCollection());
        $group = $this->attachThemes($group, $themes);
        $this->em->persist($group);
        if($flush){
            $this->em->flush();
        }
    }

    /**
     * @param Group $group
     * @param array $themes
     * @return Group
     */
    protected function attachThemes(Group $group, array $themes): Group
    {
        foreach ($themes as $theme){
            $group->addTheme($this->themeRepository->find($theme));
        }
        return $group;
    }
}