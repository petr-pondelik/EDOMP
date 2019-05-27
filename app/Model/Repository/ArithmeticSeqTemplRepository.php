<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:48
 */

namespace App\Model\Repository;

use App\Model\Traits\SequenceValTrait;

/**
 * Class ArithmeticSeqTemplRepository
 * @package App\Model\Repository
 */
class ArithmeticSeqTemplRepository extends BaseRepository
{
    use SequenceValTrait;

    /**
     * @var string
     */
    protected $tableName = "problem";
}