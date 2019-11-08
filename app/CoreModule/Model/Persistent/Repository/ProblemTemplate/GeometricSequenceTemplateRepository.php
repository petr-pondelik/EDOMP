<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:31
 */

namespace App\Model\Persistent\Repository\ProblemTemplate;

use App\Model\Persistent\Repository\BaseRepository;
use App\Model\Persistent\Traits\SequenceValTrait;

/**
 * Class GeometricSequenceTemplateRepository
 * @package App\Model\Persistent\Repository
 */
class GeometricSequenceTemplateRepository extends BaseRepository
{
    use SequenceValTrait;

    /**
     * @var string
     */
    protected $tableName = 'problem';
}