<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:31
 */

namespace App\Model\Persistent\Repository;

use App\Model\Persistent\Traits\SequenceValTrait;

/**
 * Class GeometricSeqTemplRepository
 * @package App\Model\Persistent\Repository
 */
class GeometricSeqTemplRepository extends BaseRepository
{
    use SequenceValTrait;

    /**
     * @var string
     */
    protected $tableName = 'problem';
}