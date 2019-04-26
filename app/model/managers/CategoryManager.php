<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.4.19
 * Time: 0:13
 */

namespace App\Model\Managers;

use App\Model\Entities\Category;
use Dibi\Fluent;
use Nette\Utils\Strings;

/**
 * Class CategoryManager
 * @package App\Model\Managers
 */
class CategoryManager extends BaseManager
{
    /**
     * @var string
     */
    protected $table = 'category';

    /**
     * @var string
     */
    protected $rowClass = Category::class;

    /**
     * @var array
     */
    protected $selectColumns = [
        'category_id',
        'label',
        'created'
    ];

    /**
     * @var string
     */
    protected $labelCol = 'label';

    /**
     * @param int $categoryId
     * @param int $limit
     * @param int $offset
     * @param array $filters
     * @return array[]|\Dibi\Row[]
     * @throws \Dibi\Exception
     */
    public function getProblemsFiltered(int $categoryId, int $limit, int $offset, array $filters)
    {
        $query = $this->db->select(
            "category.category_id, category.label AS category_label, sub_category.label AS sub_category_label,
                   problem.text_before, problem.structure, problem.text_after, difficulty.label AS difficulty_label,
                   problem_final.result AS result, problem_final.problem_id"
        )
            ->from($this->table)
            ->join("sub_category")
            ->using("(category_id)")
            ->join("problem")
            ->using("(sub_category_id)")
            ->join("problem_final")
            ->using("(problem_id)")
            ->join("difficulty")
            ->using("(difficulty_id)");

        $query = $this->applyFilters($query, $filters);

        return $query
            ->where("category.category_id = ?", $categoryId)
            ->limit($limit)
            ->offset($offset)
            ->execute()
            ->fetchAll();
    }

    /**
     * @param int $categoryId
     * @param array $filters
     * @return int
     */
    public function getProblemsFilteredCnt(int $categoryId, array $filters)
    {
        $query = $this->db->select("problem.problem_id")
            ->from($this->table)
            ->join("sub_category")
            ->using("(category_id)")
            ->join("problem")
            ->using("(sub_category_id)")
            ->join("problem_final")
            ->using("(problem_id)");

        $query = $this->applyFilters($query, $filters);

        return $query
            ->where("category.category_id = ?", $categoryId)
            ->count();
    }

    /**
     * @param Fluent $query
     * @param array $filters
     * @return Fluent
     */
    private function applyFilters(Fluent $query, array $filters): Fluent
    {
        if(isset($filters["difficulty"]))
            $query = $query->where("problem.difficulty_id IN (?)", $filters["difficulty"]);

        if(isset($filters["theme"]))
            $query = $query->where("problem.sub_category_id IN (?)", $filters["theme"]);

        if(isset($filters["result"])){
            if( in_array("0", $filters["result"]) && !in_array("1", $filters["result"]) )
                $query = $query->where("problem_final.result IS NOT NULL AND problem_final.result <> ''");
            else if( !in_array("0", $filters["result"]) && in_array("1", $filters["result"]))
                $query = $query->where("problem_final.result = ''");
        }

        if(isset($filters["sort_by_difficulty"])){
            switch($filters["sort_by_difficulty"]){
                case 0: $query = $query->orderBy("problem.problem_id", "ASC");
                        break;
                case 1: $query = $query->orderBy("problem.difficulty_id", "ASC");
                        break;
                case 2: $query = $query->orderBy("problem.difficulty_id", "DESC");
                        break;
            }
        }

        return $query;
    }
}