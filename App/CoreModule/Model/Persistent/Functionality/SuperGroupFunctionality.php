<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 22:14
 */

namespace App\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\SuperGroup;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use App\CoreModule\Model\Persistent\Repository\SuperGroupRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;

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
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        $superGroup = new SuperGroup();
        $superGroup->setLabel($data->label);
        if (isset($data->userId)) {
            $superGroup->setCreatedBy($this->userRepository->find($data->userId));
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
     */
    public function update(int $id, iterable $data, bool $flush = true): ?BaseEntity
    {
        $superGroup = $this->repository->find($id);
        $superGroup->setLabel($data->label);
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
     */
    public function updatePermissions(int $id, $themes, bool $flush = true): void
    {
        $superGroup = $this->repository->find($id);
        $superGroup->setThemes(new ArrayCollection());
        $superGroup = $this->attachThemes($superGroup, $themes);
        foreach ($superGroup->getGroups()->getValues() as $group) {
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
     */
    protected function attachThemes(SuperGroup $superGroup, array $themes): SuperGroup
    {
        foreach ($themes as $theme) {
            $superGroup->addTheme($this->themeRepository->find($theme));
        }
        return $superGroup;
    }
}