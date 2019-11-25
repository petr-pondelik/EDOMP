<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 19:00
 */

namespace App\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\Logo;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\LogoRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Class LogoFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality
 */
class LogoFunctionality extends BaseFunctionality
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * LogoFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param LogoRepository $logoRepository
     * @param UserRepository $userRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        LogoRepository $logoRepository,
        UserRepository $userRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $logoRepository;
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
        $logo = new Logo();

        $logo->setExtensionTmp($data->extension_tmp);
        $logo->setLabel('label');

        if (isset($data->createdBy)) {
            $logo->setCreatedBy($this->userRepository->find($data->createdBy));
        }

        $this->em->persist($logo);

        if ($flush) {
            $this->em->flush();
        }

        return $logo;
    }

    /**
     * @param int $id
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function update(int $id, iterable $data, bool $flush = true): ?BaseEntity
    {
        bdump('UPDATE');
        bdump($data);
        /**
         * @var Logo $logo
         */
        $logo = $this->repository->find($id);

        if (!$logo) {
            throw new EntityNotFoundException('Entity for update not found.');
        }
        if (isset($data->extension_tmp)) {
            $logo->setExtensionTmp($data->extension_tmp);
        }
        if (isset($data->extension)) {
            $logo->setExtension($data->extension);
        }
        if (isset($data->path)) {
            $logo->setPath($data->path);
        }
        if (isset($data->label)) {
            $logo->setLabel($data->label);
        }
        if (isset($data->createdBy)) {
            $logo->setCreatedBy($this->userRepository->find($data->createdBy));
        }


        $this->em->persist($logo);

        if ($flush) {
            $this->em->flush();
        }

        return $logo;
    }

    public function deleteEmpty(): void
    {
        $this->repository->createQueryBuilder('er')
            ->delete()
            ->where('er.path IS NULL')
            ->getQuery()->execute();
    }
}