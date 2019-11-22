<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 23:30
 */

namespace App\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\SubTheme;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;

/**
 * Class SubThemeFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality
 */
class SubThemeFunctionality extends BaseFunctionality
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
     * SubThemeFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param SubThemeRepository $subThemeRepository
     * @param ThemeRepository $themeRepository
     * @param UserRepository $userRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        SubThemeRepository $subThemeRepository,
        ThemeRepository $themeRepository,
        UserRepository $userRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $subThemeRepository;
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
        $subtheme = new SubTheme();
        $theme = $this->themeRepository->find($data->theme);
        $subtheme->setLabel($data->label);
        $subtheme->setTheme($theme);
        $subtheme->setCreatedBy($this->userRepository->find($data->userId));
        $this->em->persist($subtheme);
        if ($flush) {
            $this->em->flush();
        }
        return $subtheme;
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
        $subtheme = $this->repository->find($id);
        if(!empty($data->label)){
            $subtheme->setLabel($data->label);
        }
        if(!empty($data->theme)) {
            $theme = $this->themeRepository->find($data->theme);
            $subtheme->setTheme($theme);
        }
        $this->em->persist($subtheme);
        if ($flush) {
            $this->em->flush();
        }
        return $subtheme;
    }
}