<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 10.4.19
 * Time: 19:16
 */

namespace App\Model\Managers;

use App\Model\Entities\SubCategory;

/**
 * Class SubCategoryManager
 * @package App\Model\Managers
 */
class SubCategoryManager extends BaseManager
{
    /**
     * @var string
     */
    protected $table = 'sub_category';

    /**
     * @var string
     */
    protected $rowClass = SubCategory::class;

    /**
     * @var array
     */
    protected $selectColumns = [
        'sub_category_id',
        'label',
        'category_id',
        'created'
    ];

    /**
     * @var string
     */
    protected $labelCol = 'label';
}