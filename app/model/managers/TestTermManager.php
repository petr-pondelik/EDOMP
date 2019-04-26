<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.4.19
 * Time: 21:35
 */

namespace App\Model\Managers;

use App\Model\Entities\TestTerm;

/**
 * Class TestTypeManager
 * @package app\model\managers
 */
class TestTermManager extends BaseManager
{
    /**
     * @var string
     */
    protected $table = 'test_term';

    /**
     * @var string
     */
    protected $rowClass = TestTerm::class;

    /**
     * @var string
     */
    protected $labelCol = 'label';

    /**
     * @var array
     */
    protected $selectColumns = [
        'test_term_id',
        'label'
    ];
}