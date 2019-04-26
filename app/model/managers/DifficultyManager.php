<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 17.2.19
 * Time: 14:43
 */

namespace App\Model\Managers;

use App\Model\Entities\Difficulty;

/**
 * Class DifficultyManager
 * @package App\Model\Managers
 */
class DifficultyManager extends BaseManager
{
    /**
     * @var string
     */
    protected $table = 'difficulty';

    /**
     * @var string
     */
    protected $rowClass = Difficulty::class;

    /**
     * @var string
     */
    protected $labelCol = 'label';

    /**
     * @var array
     */
    protected $selectColumns = [
        'difficulty_id',
        'label'
    ];
}