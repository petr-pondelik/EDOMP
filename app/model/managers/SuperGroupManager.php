<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.4.19
 * Time: 22:21
 */

namespace App\Model\Managers;

use App\Helpers\ConstHelper;
use App\Model\Entities\SuperGroup;
use Dibi\Connection;
use Nette\NotSupportedException;

/**
 * Class SuperGroupManager
 * @package App\Model\Managers
 */
class SuperGroupManager extends BaseManager
{
    /**
     * @var string
     */
    protected $table = "super_group";

    /**
     * @var string
     */
    protected $categoryRelTable = "super_group_category_rel";

    /**
     * @var string
     */
    protected $rowClass = SuperGroup::class;

    /**
     * @var string
     */
    protected $labelCol = 'label';

    /**
     * @var array
     */
    protected $selectColumns = [
        'super_group_id',
        'label',
        'created'
    ];

    /**
     * @var GroupManager
     */
    protected $groupManager;

    /**
     * SuperGroupManager constructor.
     * @param GroupManager $groupManager
     * @param ConstHelper $constHelper
     * @param Connection $connection
     * @param string|null $table
     */
    public function __construct
    (
        GroupManager $groupManager,
        ConstHelper $constHelper, Connection $connection, string $table = null
    )
    {
        parent::__construct($constHelper, $connection, $table);
        $this->groupManager = $groupManager;
    }

    public function getSelect(string $order = 'DESC', $select = null, ...$args)
    {
        if(empty($this->selectColumns)){
            throw new NotSupportedException('Empty select column list. Did you specified [selectColumns] attribute?');
        }
        else{
            return $this->db->select($select !== null ? array_merge($this->selectColumns, (array)$select) : $this->selectColumns, ...$args)
                ->from($this->table)
                ->orderBy($this->getPrimary().' '.$order)
                ->where("super_group_id != " . $this->constHelper::ADMIN_SUPERGROUP);
        }
    }

    /**
     * @param int $superGroupId
     * @return array
     * @throws \Dibi\Exception
     */
    public function getCategoriesIds(int $superGroupId): array
    {
        $res = $this->db->select($this->categoryRelTable . ".category_id")
            ->from($this->table)
            ->join($this->categoryRelTable)
            ->using("(super_group_id)")
            ->where($this->table . "." . $this->getPrimary() . " = " . $superGroupId)
            ->execute()
            ->fetchPairs();
        return $res;
    }

    /**
     * @param int $superGroupId
     * @param iterable $categories
     * @throws \Dibi\Exception
     */
    public function attachCategories(int $superGroupId, iterable $categories)
    {
        foreach($categories as $category){
            $this->db->insert($this->categoryRelTable, [
                "super_group_id" => $superGroupId,
                "category_id" => $category
            ])->execute();
        }
    }

    /**
     * @param int $superGroupId
     * @return \Dibi\Result|int
     * @throws \Dibi\Exception
     */
    public function detachCategories(int $superGroupId)
    {
        return $this->db->delete($this->categoryRelTable)
            ->where($this->getPrimary() . " = " . $superGroupId)
            ->execute();
    }

    /**
     * @param int $superGroupId
     * @param iterable $categories
     * @throws \Dibi\Exception
     */
    public function updatePermissions(int $superGroupId, iterable $categories)
    {
        $this->detachCategories($superGroupId);
        $this->attachCategories($superGroupId, $categories);
        $groups = $this->groupManager->getByCond("super_group_id = " . $superGroupId);
        foreach ($groups as $group){
            $this->groupManager->detachCategories($group->group_id);
            $this->groupManager->attachCategories($group->group_id, $categories);
        }
    }
}