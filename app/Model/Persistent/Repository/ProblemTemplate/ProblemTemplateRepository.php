<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 10:21
 */

namespace App\Model\Persistent\Repository\ProblemTemplate;

use App\Model\Persistent\Repository\BaseRepository;
use App\Model\Persistent\Traits\FilterTrait;
use App\Model\Persistent\Traits\SequenceValTrait;

/**
 * Class ProblemTemplateRepository
 * @package App\Model\Persistent\Repository
 */
class ProblemTemplateRepository extends BaseRepository
{
    use SequenceValTrait;

    use FilterTrait;

    /**
     * @var string
     */
    protected $tableName = 'problem';
}