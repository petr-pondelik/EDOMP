<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 23:33
 */

namespace App\Model\Persistent\Repository;

use App\Model\Persistent\Traits\FilterTrait;

/**
 * Class ProblemRepository
 * @package App\Model\Persistent\Repository
 */
class ProblemRepository extends BaseRepository
{
    use FilterTrait;
}