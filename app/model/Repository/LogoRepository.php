<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 19:00
 */

namespace App\Model\Repository;

/**
 * Class LogoRepository
 * @package App\Model\Repository
 */
class LogoRepository extends BaseRepository
{
    /**
     * @return int
     */
    public function getSequenceVal(): int
    {
        $sql = "SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'edomp_final' AND TABLE_NAME = 'logo'";
        return $this->getEntityManager()->getConnection()->query($sql)->fetch()["AUTO_INCREMENT"];
    }
}