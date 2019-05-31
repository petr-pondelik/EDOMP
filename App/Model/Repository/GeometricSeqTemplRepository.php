<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:31
 */

namespace App\Model\Repository;

use App\Model\Traits\SequenceValTrait;

/**
 * Class GeometricSeqTemplRepository
 * @package App\Model\Repository
 */
class GeometricSeqTemplRepository extends BaseRepository
{
    use SequenceValTrait;

    /**
     * @var string
     */
    protected $tableName = "problem";
}