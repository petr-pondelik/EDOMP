<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 13:04
 */

namespace App\Model\Traits;

/**
 * Trait SequenceValTrait
 * @package App\Model\Traits
 */
trait SequenceValTrait
{
    /**
     * @return int
     */
    public function getSequenceVal(): int
    {
        $sql = "SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'edomp' AND TABLE_NAME = '$this->tableName'";
        return $this->getEntityManager()->getConnection()->query($sql)->fetch()["AUTO_INCREMENT"];
    }
}