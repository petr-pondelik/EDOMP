<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:29
 */

namespace App\CoreModule\Model\Persistent\Repository\ProblemFinal;

use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Model\Persistent\Entity\Problem;
use App\CoreModule\Model\Persistent\Repository\SecuredRepository;
use Doctrine\ORM\Mapping;

/**
 * Class QuadraticEquationFinalRepository
 * @package App\CoreModule\Model\Persistent\Repository\ProblemFinal
 */
class QuadraticEquationFinalRepository extends SecuredRepository
{
    /**
     * QuadraticEquationFinalRepository constructor.
     * @param $em
     * @param Mapping\ClassMetadata $class
     * @param ConstHelper $constHelper
     */
    public function __construct($em, Mapping\ClassMetadata $class, ConstHelper $constHelper)
    {
        parent::__construct($em, $class, $constHelper);
        $this->tableName = $this->getEntityManager()->getClassMetadata(Problem::class)->getTableName();
    }
}