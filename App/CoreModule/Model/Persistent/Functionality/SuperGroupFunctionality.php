<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 22:14
 */

namespace App\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\Group;
use App\CoreModule\Model\Persistent\Entity\SuperGroup;
use App\CoreModule\Model\Persistent\Entity\Theme;
use App\CoreModule\Model\Persistent\Entity\User;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use App\CoreModule\Model\Persistent\Repository\SuperGroupRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\DateTime;

/**
 * Class SuperGroupFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality
 */
class SuperGroupFunctionality extends BaseFunctionality
{
    /**
     * @var ThemeRepository
     */
    protected $themeRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var GroupFunctionality
     */
    protected $groupFunctionality;

    /**
     * SuperGroupFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param SuperGroupRepository $repository
     * @param ThemeRepository $themeRepository
     * @param UserRepository $userRepository
     * @param GroupFunctionality $groupFunctionality
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager, SuperGroupRepository $repository,
        ThemeRepository $themeRepository, UserRepository $userRepository,
        GroupFunctionality $groupFunctionality
    )
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
        $this->themeRepository = $themeRepository;
        $this->userRepository = $userRepository;
        $this->groupFunctionality = $groupFunctionality;
    }

    /**
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws EntityNotFoundException
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        $superGroup = new SuperGroup();

        $superGroup->setLabel($data['label']);

        if (isset($data['userId'])) {
            /** @var User|null $user */
            $user = $this->userRepository->find($data['userId']);
            if (!$user) {
                throw new EntityNotFoundException('User not found.');
            }
            $superGroup->setCreatedBy($user);
        }

        if (isset($data['created'])) {
            $superGroup->setCreated(DateTime::from($data['created']));
        }

        $this->em->persist($superGroup);

        if ($flush) {
            $this->em->flush();
        }

        return $superGroup;
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
        /** @var SuperGroup|null $superGroup */
        $superGroup = $this->repository->find($id);
        if (!$superGroup) {
            throw new EntityNotFoundException('SuperGroup for update not found.');
        }

        $superGroup->setLabel($data['label']);

        $this->em->persist($superGroup);

        if ($flush) {
            $this->em->flush();
        }

        return $superGroup;
    }

    /**
     * @param int $id
     * @param $themes
     * @param bool $flush
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function updatePermissions(int $id, $themes, bool $flush = true): void
    {
        /** @var SuperGroup|null $superGroup */
        $superGroup = $this->repository->find($id);
        if (!$superGroup) {
            throw new EntityNotFoundException('SuperGroup for update-permissions not found.');
        }

        $superGroup->setThemes(new ArrayCollection());
        $superGroup = $this->attachThemes($superGroup, $themes);

        foreach ($superGroup->getGroups()->getValues() as $group) {
            /** @var Group $group */
            $this->groupFunctionality->updatePermissions($group->getId(), $themes);
        }

        $this->em->persist($superGroup);

        if ($flush) {
            $this->em->flush();
        }
    }

    /**
     * @param SuperGroup $superGroup
     * @param array $themes
     * @return SuperGroup
     * @throws EntityNotFoundException
     */
    protected function attachThemes(SuperGroup $superGroup, array $themes): SuperGroup
    {
        foreach ($themes as $theme) {
            /** @var Theme|null $theme */
            $theme = $this->themeRepository->find($theme);
            if (!$theme) {
                throw new EntityNotFoundException('Theme not found.');
            }
            $superGroup->addTheme($theme);
        }
        return $superGroup;
    }
}