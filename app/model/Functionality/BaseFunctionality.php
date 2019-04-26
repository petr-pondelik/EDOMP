<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:14
 */

namespace App\Model\Functionality;

use App\Model\Repository\BaseRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class BaseFunctionality
 * @package App\Model\Functionality
 */
abstract class BaseFunctionality
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var BaseRepository
     */
    protected $repository;

    /**
     * BaseFunctionality constructor.
     * @param EntityManager $entityManager
     */
    public function __construct
    (
        EntityManager $entityManager
    )
    {
        $this->em = $entityManager;
    }

    /**
     * @param ArrayHash $data
     * @return void
     */
    abstract public function create(ArrayHash $data): void;

    /**
     * @param int $id
     */
    abstract public function delete(int $id): void;

    /**
     * @param int $id
     * @param ArrayHash $data
     */
    abstract public function update(int $id, ArrayHash $data): void;
}