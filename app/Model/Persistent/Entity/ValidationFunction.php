<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.8.19
 * Time: 16:41
 */

namespace App\Model\Persistent\Entity;

use App\Model\Persistent\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="")
 *
 * Class ValidationFunction
 * @package App\Model\Persistent\Entity
 */
class ValidationFunction extends BaseEntity
{
    use LabelTrait;

    /**
     * @var string
     */
    protected $toStringAttr = 'label';

    /**
     * @ORM\OneToMany(targetEntity="ProblemCondition", mappedBy="validationFunction", cascade={"all"})
     *
     * @var ArrayCollection
     */
    protected $problemConditions;

    /**
     * ValidationFunction constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->problemConditions = new ArrayCollection();
    }
}