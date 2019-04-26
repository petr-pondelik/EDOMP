<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.4.19
 * Time: 13:55
 */

namespace App\Model\Managers;

/**
 * Class HomepageStatisticsManager
 * @package App\Model\Managers
 */
class HomepageStatisticsManager extends BaseManager
{
    /**
     * @return int
     */
    public function getProblemPrototypeCnt(): int
    {
        return $this->getCnt("problem_prototype");
    }

    /**
     * @return int
     */
    public function getProblemFinalCnt(): int
    {
        return $this->getCnt("problem_final");
    }

    /**
     * @return int
     */
    public function getCategoryCnt(): int
    {
        return $this->getCnt("category");
    }

    /**
     * @return int
     */
    public function getSubCategoryCnt(): int
    {
        return $this->getCnt("sub_category");
    }

    /**
     * @return int
     */
    public function getTestCnt(): int
    {
        return $this->getCnt("test");
    }

    /**
     * @return int
     */
    public function getUserCnt(): int
    {
        return $this->getCnt("user");
    }

    /**
     * @return int
     */
    public function getGroupCnt(): int
    {
        return $this->getCnt("group");
    }

    /**
     * @return int
     */
    public function getSuperGroupCnt(): int
    {
        return $this->getCnt("super_group");
    }

    /**
     * @return int
     */
    public function getLogoCnt(): int
    {
        return$this->getCnt("logo");
    }

    /**
     * @param string $table
     * @return int
     */
    private function getCnt(string $table): int
    {
        return $this->db->select("COUNT(*)")
            ->from($table)
            ->fetchSingle();
    }
}