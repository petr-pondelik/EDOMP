<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 10:22
 */

namespace App\Model\Entity;

use App\Model\Traits\LabelTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\TermRepository")
 *
 * Class Term
 * @package App\Model\Entity
 */
class Term extends BaseEntity
{
    use LabelTrait;

    /**
     * @var string
     */
    protected $toStringAttr = "label";
}