<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 10:21
 */

namespace App\CoreModule\Model\Persistent\Repository\ProblemTemplate;

use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Model\Persistent\Entity\Problem;
use App\CoreModule\Model\Persistent\Repository\SecuredRepository;
use App\CoreModule\Model\Persistent\Traits\FilterTrait;
use Doctrine\ORM\Mapping;

/**
 * Class ProblemTemplateRepository
 * @package App\CoreModule\Model\Persistent\Repository\ProblemTemplate
 */
class ProblemTemplateRepository extends SecuredRepository
{
    use FilterTrait;

    /**
     * ProblemTemplateRepository constructor.
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