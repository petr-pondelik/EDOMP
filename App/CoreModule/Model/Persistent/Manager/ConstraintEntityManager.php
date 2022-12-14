<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 31.5.19
 * Time: 11:17
 */

namespace App\CoreModule\Model\Persistent\Manager;


use App\CoreModule\Exceptions\EntityException;
use Kdyby\Doctrine\EntityManager;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ConstraintEntityManager
 * @package App\CoreModule\Model\Persistent\Manager
 */
class ConstraintEntityManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * ConstraintEntityManager constructor.
     * @param EntityManager $em
     * @param ValidatorInterface $validator
     */
    public function __construct
    (
        EntityManager $em, ValidatorInterface $validator
    )
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * @param array|object $entity
     * @return ConstraintEntityManager
     * @throws EntityException
     */
    public function persist($entity): ConstraintEntityManager
    {
        $this->validateEntity($entity);
        $this->em->persist($entity);
        return $this;
    }

    /**
     * @param null $entity
     * @return ConstraintEntityManager
     * @throws \Exception
     */
    public function flush($entity = null): ConstraintEntityManager
    {
        $this->em->flush($entity);
        return $this;
    }

    /**
     * @param $entity
     * @throws EntityException
     */
    public function validateEntity($entity): void
    {
        $violations = $this->validator->validate($entity);
        if ($violations->count()) {
            throw new EntityException((string)$violations);
        }
    }

    /**
     * @param $entity
     * @return ConstraintEntityManager
     */
    public function remove($entity): ConstraintEntityManager
    {
        $this->em->remove($entity);
        return $this;
    }
}