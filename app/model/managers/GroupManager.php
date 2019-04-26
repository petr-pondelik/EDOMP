<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.4.19
 * Time: 21:53
 */

namespace App\Model\Managers;

use App\Model\Entities\Group;
use Nette\NotSupportedException;

/**
 * Class GroupManager
 * @package App\Model\Managers
 */
class GroupManager extends BaseManager
{
    /**
     * @var string
     */
    protected $table = "group";

    /**
     * @var string
     */
    protected $categoryRelTable = "group_category_rel";

    /**
     * @var string
     */
    protected $rowClass = Group::class;

    /**
     * @var string
     */
    protected $labelCol = "label";

    /**
     * @var array
     */
    protected $selectColumns = [
        "group_id",
        "label",
        "super_group_id",
        "created"
    ];

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
     * @param int $groupId
     * @return array
     * @throws \Dibi\Exception
     */
    public function getCategoriesIds(int $groupId): array
    {
        $res = $this->db->select($this->categoryRelTable . ".category_id")
            ->from($this->table)
            ->join($this->categoryRelTable)
            ->using("(group_id)")
            ->where($this->table . "." . $this->getPrimary() . " = " . $groupId)
            ->execute()
            ->fetchPairs();
        return $res;
    }

    /**
     * @param int $groupId
     * @param iterable $categories
     * @throws \Dibi\Exception
     */
    public function updatePermissions(int $groupId, iterable $categories)
    {
        $this->detachCategories($groupId);
        $this->attachCategories($groupId, $categories);
    }

    /**
     * @param int $groupId
     * @param iterable $categories
     * @throws \Dibi\Exception
     */
    public function attachCategories(int $groupId, iterable $categories)
    {
        foreach($categories as $category){
            $this->db->insert($this->categoryRelTable, [
                "group_id" => $groupId,
                "category_id" => $category
            ])->execute();
        }
    }

    /**
     * @param int $groupId
     * @return \Dibi\Result|int
     * @throws \Dibi\Exception
     */
    public function detachCategories(int $groupId)
    {
        return $this->db->delete($this->categoryRelTable)
            ->where($this->getPrimary() . " = " . $groupId)
            ->execute();
    }
}