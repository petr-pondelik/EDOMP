<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 13:15
 */

namespace App\Model\Repository;

use App\Model\Entity\ProblemFinal;
use App\Model\Traits\FilterTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping;

/**
 * Class ProblemFinalRepository
 * @package App\Model\Repository
 */
final class ProblemFinalRepository extends BaseRepository
{
    use FilterTrait;
}