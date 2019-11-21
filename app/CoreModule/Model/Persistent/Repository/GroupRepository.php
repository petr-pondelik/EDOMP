<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 22:03
 */

namespace App\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Helpers\ConstHelper;
use Doctrine\ORM\Mapping;

/**
 * Class GroupRepository
 * @package App\CoreModule\Model\Persistent\Repository
 */
class GroupRepository extends SecuredRepository
{
    /**
     * GroupRepository constructor.
     * @param $em
     * @param Mapping\ClassMetadata $class
     * @param ConstHelper $constHelper
     */
    public function __construct($em, Mapping\ClassMetadata $class, ConstHelper $constHelper)
    {
        parent::__construct($em, $class, $constHelper);
        $this->exclude[] = $this->constHelper::ADMIN_GROUP;
    }
}