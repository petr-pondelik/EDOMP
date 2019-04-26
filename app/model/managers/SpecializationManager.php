<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 14.3.19
 * Time: 18:26
 */

namespace App\Model\Managers;

use App\Model\Entities\Specialization;

/**
 * Class SpecializationManager
 * @package app\model\managers
 */
class SpecializationManager extends BaseManager
{
    /**
     * @var string
     */
    protected $table = 'specialization';

    /**
     * @var string
     */
    protected $rowClass = Specialization::class;

    /**
     * @var string
     */
    protected $labelCol = 'label';

    /**
     * @var array
     */
    protected $selectColumns = [
        'specialization_id',
        'label'
    ];
}