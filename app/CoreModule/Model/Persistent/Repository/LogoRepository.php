<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 19:00
 */

namespace App\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Traits\SequenceValTrait;

/**
 * Class LogoRepository
 * @package App\CoreModule\Model\Persistent\Repository
 */
class LogoRepository extends SecuredRepository
{
    use SequenceValTrait;
}