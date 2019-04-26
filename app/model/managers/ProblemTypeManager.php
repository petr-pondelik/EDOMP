<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.2.19
 * Time: 17:59
 */

namespace App\Model\Managers;

use App\Model\Entities\ProblemType;

/**
 * Class EntityManager
 * @package App\Model\Managers
 */
class ProblemTypeManager extends BaseManager
{
    /**
     * @var string
     */
    protected $table = 'problem_type';

    /**
     * @var string
     */
    protected $rowClass = ProblemType::class;

    /**
     * @var string
     */
    protected $labelCol = 'label';

    /**
     * @var array
     */
    protected $selectColumns = [
        'problem_type_id',
        'label',
        'is_generatable'
    ];

    /**
     * @param int $problemTypeId
     * @return array
     * @throws \Dibi\Exception
     */
    public function getTypeConditionTypes(int $problemTypeId)
    {
        return $this->db->query('
            SELECT problem_type_id
            FROM problem_type
            INNER JOIN condition_type USING(problem_type_id)
        ')->fetchAssoc('problem_type_id');
    }
}