<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:14
 */

namespace App\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\BaseRepository;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Class BaseFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality
 */
abstract class BaseFunctionality
{
    /**
     * @var ConstraintEntityManager
     */
    protected $em;

    /**
     * @var BaseRepository
     */
    protected $repository;

    /**
     * BaseFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager
    )
    {
        $this->em = $entityManager;
    }

    /**
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     */
    abstract public function create(iterable $data, bool $flush = true): ?BaseEntity;

    /**
     * @param int $id
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     */
    abstract public function update(int $id, iterable $data, bool $flush = true): ?BaseEntity;

    /**
     * @param int $id
     * @param bool $flush
     * @return bool
     * @throws EntityNotFoundException
     */
    public function delete(int $id, bool $flush = true): bool
    {
        $theme = $this->repository->find($id);
        if(!$theme){
            throw new EntityNotFoundException('Entity for deletion was not found.');
        }
        $this->em->remove($theme);
        if($flush){
            $this->em->flush();
        }
        return true;
    }
}