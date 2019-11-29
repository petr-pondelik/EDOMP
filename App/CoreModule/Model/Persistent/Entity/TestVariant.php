<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 17.7.19
 * Time: 21:58
 */

namespace App\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TestVariant
 * @package App\CoreModule\Model\Persistent\Entity
 *
 * @ORM\Entity(repositoryClass="App\CoreModule\Model\Persistent\Repository\TestVariantRepository")
 */
class TestVariant extends BaseEntity
{
    use LabelTrait;

    /**
     * @var string
     */
    protected $toStringAttr = 'label';

    /**
     * @ORM\ManyToOne(targetEntity="App\CoreModule\Model\Persistent\Entity\Test", inversedBy="testVariants", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *      message="Test can't be blank."
     * )
     *
     * @var Test
     */
    protected $test;

    /**
     * @ORM\OneToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\ProblemFinalTestVariantAssociation", mappedBy="testVariant", cascade={"all"})
     * @Assert\NotBlank(
     *     message="ProblemFinalAssociations can't be blank."
     * )
     *
     * @var PersistentCollection|ArrayCollection
     */
    protected $problemFinalAssociations;

    /**
     * TestVariant constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->problemFinalAssociations = new ArrayCollection();
    }

    /**
     * @return Test
     */
    public function getTest(): Test
    {
        return $this->test;
    }

    /**
     * @param Test $test
     */
    public function setTest(Test $test): void
    {
        $this->test = $test;
    }

    /**
     * @return PersistentCollection|ArrayCollection
     */
    public function getProblemFinalAssociations()
    {
        return $this->problemFinalAssociations;
    }

    /**
     * @param ProblemFinalTestVariantAssociation[] $problemFinalAssociations
     */
    public function setProblemFinalAssociations($problemFinalAssociations = null): void
    {
        $this->problemFinalAssociations = $problemFinalAssociations;
    }

    /**
     * @param ProblemFinalTestVariantAssociation $association
     */
    public function addProblemFinalAssociation(ProblemFinalTestVariantAssociation $association): void
    {
        if($this->problemFinalAssociations->contains($association)){
            return;
        }
        $this->problemFinalAssociations[] = $association;
    }
}