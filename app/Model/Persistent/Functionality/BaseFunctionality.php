<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:14
 */

namespace App\Model\Persistent\Functionality;

use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\BaseRepository;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;

/**
 * Class BaseFunctionality
 * @package App\Model\Persistent\Functionality
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
     * @param ArrayHash $data
     * @param bool $flush
     * @return BaseEntity|null
     */
    abstract public function create(ArrayHash $data, bool $flush = true): ?BaseEntity;

    /**
     * @param int $id
     * @param ArrayHash $data
     * @param bool $flush
     * @return BaseEntity|null
     */
    abstract public function update(int $id, ArrayHash $data, bool $flush = true): ?BaseEntity;

    /**
     * @param int $id
     * @param bool $flush
     * @return bool
     * @throws EntityNotFoundException
     */
    public function delete(int $id, bool $flush = true): bool
    {
        $category = $this->repository->find($id);
        if(!$category){
            throw new EntityNotFoundException('Entity for deletion was not found.');
        }
        $this->em->remove($category);
        if($flush){
            $this->em->flush();
        }
        return true;
    }
}