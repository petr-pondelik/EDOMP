<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 13:26
 */

namespace App\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Helpers\ConstHelper;
use Doctrine\ORM\Mapping;
use Kdyby\Doctrine\EntityRepository;

/**
 * Class BaseRepository
 * @package App\CoreModule\Model\Persistent\Repository
 */
abstract class BaseRepository extends EntityRepository
{
    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * BaseRepository constructor.
     * @param $em
     * @param Mapping\ClassMetadata $class
     * @param ConstHelper $constHelper
     */
    public function __construct($em, Mapping\ClassMetadata $class, ConstHelper $constHelper)
    {
        parent::__construct($em, $class);
        $this->constHelper = $constHelper;
    }

    /**
     * @return int
     */
    public function getCnt(): int
    {
        return count($this->findAll());
    }
}