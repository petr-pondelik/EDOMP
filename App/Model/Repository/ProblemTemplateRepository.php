<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 10:21
 */

namespace App\Model\Repository;

use App\Model\Traits\FilterTrait;
use App\Model\Traits\SequenceValTrait;

/**
 * Class ProblemTemplateRepository
 * @package App\Model\Repository
 */
class ProblemTemplateRepository extends BaseRepository
{
    use SequenceValTrait;

    use FilterTrait;

    /**
     * @var string
     */
    protected $tableName = "problem";
}