<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 23:33
 */

namespace App\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Traits\FilterTrait;

/**
 * Class ProblemRepository
 * @package App\CoreModule\Model\Persistent\Repository
 */
class ProblemRepository extends SecuredRepository
{
    use FilterTrait;
}