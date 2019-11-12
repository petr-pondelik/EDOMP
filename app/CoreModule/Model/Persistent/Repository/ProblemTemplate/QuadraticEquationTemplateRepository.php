<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:49
 */

namespace App\CoreModule\Model\Persistent\Repository\ProblemTemplate;

use App\CoreModule\Model\Persistent\Repository\BaseRepository;
use App\CoreModule\Model\Persistent\Traits\SequenceValTrait;

/**
 * Class QuadraticEquationTemplateRepository
 * @package App\CoreModule\Model\Persistent\Repository\ProblemTemplate
 */
class QuadraticEquationTemplateRepository extends BaseRepository
{
    use SequenceValTrait;

    /**
     * @var string
     */
    protected $tableName = 'problem';
}