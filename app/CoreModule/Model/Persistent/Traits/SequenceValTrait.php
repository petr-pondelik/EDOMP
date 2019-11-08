<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 13:04
 */

namespace App\Model\Persistent\Traits;

/**
 * Trait SequenceValTrait
 * @package App\Model\Persistent\Traits
 */
trait SequenceValTrait
{
    /**
     * @return int
     */
    public function getSequenceVal(): int
    {
        bdump($this->tableName);
        $sql = "SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'edomp' AND TABLE_NAME = '$this->tableName'";
        bdump($this->getEntityManager()->getConnection()->query($sql)->fetch());
        return $this->getEntityManager()->getConnection()->query($sql)->fetch()["AUTO_INCREMENT"];
    }
}