<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 23:33
 */

namespace App\Model\Repository;

use App\Model\Traits\FilterTrait;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Class ProblemRepository
 * @package App\Model\Repository
 */
class ProblemRepository extends BaseRepository
{
    use FilterTrait;
}