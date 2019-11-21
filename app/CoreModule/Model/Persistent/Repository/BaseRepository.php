<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 13:26
 */

namespace App\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Model\Persistent\Traits\SequenceValTrait;
use Doctrine\ORM\Mapping;
use Kdyby\Doctrine\EntityRepository;

/**
 * Class BaseRepository
 * @package App\CoreModule\Model\Persistent\Repository
 */
abstract class BaseRepository extends EntityRepository
{
    use SequenceValTrait;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var array
     */
    protected $exclude = [];

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
        $this->tableName = $this->getClassMetadata()->getTableName();
    }
}