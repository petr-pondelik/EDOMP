<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 10:21
 */

namespace App\Model\Repository;

/**
 * Class ProblemTemplateRepository
 * @package App\Model\Repository
 */
class ProblemTemplateRepository extends BaseRepository
{
    /**
     * @return int
     */
    public function getSequenceVal(): int
    {
        $sql = "SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'edomp_final' AND TABLE_NAME = 'problem_template'";
        return $this->getEntityManager()->getConnection()->query($sql)->fetch()["AUTO_INCREMENT"];
    }
}