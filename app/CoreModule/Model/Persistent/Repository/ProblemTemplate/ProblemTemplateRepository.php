<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 10:21
 */

namespace App\CoreModule\Model\Persistent\Repository\ProblemTemplate;

use App\CoreModule\Model\Persistent\Repository\BaseRepository;
use App\CoreModule\Model\Persistent\Traits\FilterTrait;
use App\CoreModule\Model\Persistent\Traits\SequenceValTrait;

/**
 * Class ProblemTemplateRepository
 * @package App\CoreModule\Model\Persistent\Repository\ProblemTemplate
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