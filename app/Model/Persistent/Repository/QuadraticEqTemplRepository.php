<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:49
 */

namespace App\Model\Persistent\Repository;

use App\Model\Persistent\Traits\SequenceValTrait;

/**
 * Class QuadraticEqTemplRepository
 * @package App\Model\Persistent\Repository
 */
class QuadraticEqTemplRepository extends BaseRepository
{
    use SequenceValTrait;

    /**
     * @var string
     */
    protected $tableName = "problem";
}