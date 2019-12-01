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
use App\CoreModule\Model\Persistent\Entity\SuperGroup;
use App\CoreModule\Model\Persistent\Entity\Theme;
use App\CoreModule\Model\Persistent\Entity\User;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use App\CoreModule\Model\Persistent\Repository\GroupRepository;
use App\CoreModule\Model\Persistent\Repository\SuperGroupRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;

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
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        $group = new Group();
        $group->setLabel($data['label']);

        /** @var SuperGroup|null $superGroup */
        $superGroup = $this->superGroupRepository->find($data['superGroup']);
        if (!$superGroup) {
            throw new EntityNotFoundException('SuperGroup not found.');
        }
        $group->setSuperGroup($superGroup);

        if (isset($data['userId'])) {
            /** @var User|null $user */
            $user = $this->userRepository->find($data['userId']);
            if (!$user) {
                throw new EntityNotFoundException('User not found.');
            }
            $group->setCreatedBy($user);
        }

        if (isset($data['created'])) {
            $group->setCreated($data['created']);
        }

        $this->em->persist($group);

        if ($flush) {
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
     * @throws EntityNotFoundException
     */
    public function update(int $id, iterable $data, bool $flush = true): ?BaseEntity
    {
        /** @var Group|null $group */
        $group = $this->repository->find($id);
        if (!$group) {
            throw new EntityNotFoundException('Group for update not found.');
        }

        if (isset($data->label)) {
            $group->setLabel($data->label);
        }

        if (isset($data->superGroup)) {
            /** @var SuperGroup|null $superGroup */
            $superGroup = $this->superGroupRepository->find($data->superGroup);
            if (!$superGroup) {
                throw new EntityNotFoundException('SuperGroup not found.');
            }
            $group->setSuperGroup($superGroup);
        }

        $this->em->persist($group);

        if ($flush) {
            $this->em->flush();
        }

        return $group;
    }

    /**
     * @param int $id
     * @param array $themes
     * @param bool $flush
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function updatePermissions(int $id, array $themes, bool $flush = true): void
    {
        /** @var Group|null $group */
        $group = $this->repository->find($id);
        if (!$group) {
            throw new EntityNotFoundException('Group to update not found.');
        }

        $group->setThemes(new ArrayCollection());
        $group = $this->attachThemes($group, $themes);

        $this->em->persist($group);

        if ($flush) {
            $this->em->flush();
        }
    }

    /**
     * @param Group $group
     * @param array $themes
     * @return Group
     * @throws EntityNotFoundException
     */
    protected function attachThemes(Group $group, array $themes): Group
    {
        foreach ($themes as $theme) {
            /** @var Theme|null $theme */
            $theme = $this->themeRepository->find($theme);
            if (!$theme) {
                throw new EntityNotFoundException('Theme to attach not found.');
            }
            $group->addTheme($theme);
        }
        return $group;
    }
}