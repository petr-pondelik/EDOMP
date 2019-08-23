<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:48
 */

namespace App\Model\Persistent\Repository;

use App\Model\Persistent\Traits\SequenceValTrait;

/**
 * Class ArithmeticSeqTemplRepository
 * @package App\Model\Persistent\Repository
 */
class ArithmeticSeqTemplRepository extends BaseRepository
{
    use SequenceValTrait;

    /**
     * @var string
     */
    protected $tableName = 'problem';
}