<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 20:49
 */

namespace App\CoreModule\Model\Persistent\Repository\ProblemFinal;

use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Model\Persistent\Entity\Problem;
use App\CoreModule\Model\Persistent\Repository\SecuredRepository;
use Doctrine\ORM\Mapping;

/**
 * Class LinearEquationFinalRepository
 * @package App\CoreModule\Model\Persistent\Repository\ProblemFinal
 */
class LinearEquationFinalRepository extends SecuredRepository
{
    /**
     * LinearEquationFinalRepository constructor.
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