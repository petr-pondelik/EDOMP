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
use App\CoreModule\Model\Persistent\Entity\Theme;
use App\CoreModule\Model\Persistent\Entity\User;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\DateTime;

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
     * @throws EntityNotFoundException
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        $subTheme = new SubTheme();

        /** @var Theme|null $theme */
        $theme = $this->themeRepository->find($data->theme);
        if (!$theme) {
            throw new EntityNotFoundException('Theme not found.');
        }

        $subTheme->setLabel($data->label);
        $subTheme->setTheme($theme);

        /** @var User|null $user */
        $user = $this->userRepository->find($data->userId);

        if (!$user) {
            throw new EntityNotFoundException('User not found.');
        }

        $subTheme->setCreatedBy($user);

        if (isset($data->created)) {
            $subTheme->setCreated(DateTime::from($data->created));
        }

        $this->em->persist($subTheme);

        if ($flush) {
            $this->em->flush();
        }

        return $subTheme;
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
        /** @var SubTheme|null $subTheme */
        $subTheme = $this->repository->find($id);

        if (!$subTheme) {
            throw new EntityNotFoundException('SubTheme to update not found.');
        }

        if (isset($data->label)) {
            $subTheme->setLabel($data->label);
        }

        if (isset($data->theme)) {
            /** @var Theme|null $theme */
            $theme = $this->themeRepository->find($data->theme);
            if (!$theme) {
                throw new EntityNotFoundException('Theme not found.');
            }
            $subTheme->setTheme($theme);
        }

        $this->em->persist($subTheme);

        if ($flush) {
            $this->em->flush();
        }

        return $subTheme;
    }
}