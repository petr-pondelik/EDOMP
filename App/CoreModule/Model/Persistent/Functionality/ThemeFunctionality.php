<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:13
 */

declare(strict_types=1);

namespace App\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\Theme;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use Nette\Utils\DateTime;

/**
 * Class ThemeFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality
 */
class ThemeFunctionality extends BaseFunctionality
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * ThemeFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param ThemeRepository $themeRepository
     * @param UserRepository $userRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        ThemeRepository $themeRepository,
        UserRepository $userRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $themeRepository;
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
        $theme = new Theme();
        $theme->setLabel($data->label);
        $theme->setCreatedBy($this->userRepository->find($data->userId));

        if (isset($data->created)) {
            $theme->setCreated(DateTime::from($data->created));
        }

        $this->em->persist($theme);

        if ($flush) {
            $this->em->flush();
        }

        return $theme;
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
        $theme = $this->repository->find($id);
        $theme->setLabel($data->label);
        $this->em->persist($theme);
        if ($flush) {
            $this->em->flush();
        }
        return $theme;
    }
}