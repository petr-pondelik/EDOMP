<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 9.12.19
 * Time: 13:01
 */

namespace App\TeacherModule\ValueObject;

use App\TeacherModule\Exceptions\HomepageStatisticsException;

/**
 * Class HomepageStatistics
 * @package App\TeacherModule\ValueObject
 */
final class HomepageStatistics
{
    /**
     * @var int
     */
    private $themeCnt;

    /**
     * @var int
     */
    private $subThemeCnt;

    /**
     * @var int
     */
    private $problemTemplateCnt;

    /**
     * @var int
     */
    private $problemFinalCnt;

    /**
     * @var int
     */
    private $testCnt;

    /**
     * @var int
     */
    private $superGroupCnt;

    /**
     * @var int
     */
    private $groupCnt;

    /**
     * @var int
     */
    private $userCnt;

    /**
     * @var int
     */
    private $logoCnt;

    /**
     * @var array
     */
    private static $keyMap = [
        0 => 'ThemeCnt',
        1 => 'SubThemeCnt',
        2 => 'TestCnt',
        3 => 'SuperGroupCnt',
        4 => 'GroupCnt',
        5 => 'UserCnt',
        6 => 'LogoCnt'
    ];

    /**
     * HomepageStatistics constructor.
     * @param iterable $statisticsValues
     * @throws HomepageStatisticsException
     */
    public function __construct(iterable $statisticsValues)
    {
        foreach ($statisticsValues as $key => $value) {
            if (isset(self::$keyMap[$key])) {
                $this->{'set' . self::$keyMap[$key]}($value['cnt']);
            } else if (property_exists(self::class, $key)) {
                $this->{'set' . $key}($value);
            } else {
                throw new HomepageStatisticsException('Not supported statistics item.');
            }
        }
    }

    /**
     * @param int $themeCnt
     * @return HomepageStatistics
     */
    public function setThemeCnt(int $themeCnt): HomepageStatistics
    {
        $this->themeCnt = $themeCnt;
        return $this;
    }

    /**
     * @return int
     */
    public function getThemeCnt(): int
    {
        return $this->themeCnt;
    }

    /**
     * @param int $subThemeCnt
     * @return HomepageStatistics
     */
    public function setSubThemeCnt(int $subThemeCnt): HomepageStatistics
    {
        $this->subThemeCnt = $subThemeCnt;
        return $this;
    }

    /**
     * @return int
     */
    public function getSubThemeCnt(): int
    {
        return $this->subThemeCnt;
    }

    /**
     * @param int $problemTemplateCnt
     * @return HomepageStatistics
     */
    public function setProblemTemplateCnt(int $problemTemplateCnt): HomepageStatistics
    {
        $this->problemTemplateCnt = $problemTemplateCnt;
        return $this;
    }

    /**
     * @return int
     */
    public function getProblemTemplateCnt(): int
    {
        return $this->problemTemplateCnt;
    }

    /**
     * @param int $problemFinalCnt
     * @return HomepageStatistics
     */
    public function setProblemFinalCnt(int $problemFinalCnt): HomepageStatistics
    {
        $this->problemFinalCnt = $problemFinalCnt;
        return $this;
    }

    /**
     * @return int
     */
    public function getProblemFinalCnt(): int
    {
        return $this->problemFinalCnt;
    }

    /**
     * @param int $testCnt
     * @return HomepageStatistics
     */
    public function setTestCnt(int $testCnt): HomepageStatistics
    {
        $this->testCnt = $testCnt;
        return $this;
    }

    /**
     * @return int
     */
    public function getTestCnt(): int
    {
        return $this->testCnt;
    }

    /**
     * @param int $superGroupCnt
     * @return HomepageStatistics
     */
    public function setSuperGroupCnt(int $superGroupCnt): HomepageStatistics
    {
        $this->superGroupCnt = $superGroupCnt;
        return $this;
    }

    /**
     * @return int
     */
    public function getSuperGroupCnt(): int
    {
        return $this->superGroupCnt;
    }

    /**
     * @param int $groupCnt
     * @return HomepageStatistics
     */
    public function setGroupCnt(int $groupCnt): HomepageStatistics
    {
        $this->groupCnt = $groupCnt;
        return $this;
    }

    /**
     * @return int
     */
    public function getGroupCnt(): int
    {
        return $this->groupCnt;
    }

    /**
     * @param int $userCnt
     * @return HomepageStatistics
     */
    public function setUserCnt(int $userCnt): HomepageStatistics
    {
        $this->userCnt = $userCnt;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserCnt(): int
    {
        return $this->userCnt;
    }

    /**
     * @param int $logoCnt
     * @return HomepageStatistics
     */
    public function setLogoCnt(int $logoCnt): HomepageStatistics
    {
        $this->logoCnt = $logoCnt;
        return $this;
    }

    /**
     * @return int
     */
    public function getLogoCnt(): int
    {
        return $this->logoCnt;
    }
}