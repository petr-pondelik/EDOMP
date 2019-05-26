<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 18:41
 */

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\TestRepository")
 *
 * Class Test
 * @package App\Model\Entity
 */
class Test extends BaseEntity
{
    /**
     * @var string
     */
    protected $toStringAttr = "id";

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Type(
     *     type="string",
     *     message="InstroductionText must be {{ type }}."
     * )
     * @var string
     */
    protected $introductionText;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(
     *     message="SchoolYear can't be blank."
     * )
     * @Assert\Regex(
     *     value="/[0-9]{4}(\/|\-)([0-9]{4}|[0-9]{2})/",
     *     message="SchoolYear is not valid."
     * )
     *
     * @var string
     */
    protected $schoolYear;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank(
     *     message="TestNumber can't be blank."
     * )
     * @Assert\Type(
     *     type="int",
     *     message="TestNumber must be {{ type }}."
     * )
     * @Assert\GreaterThanOrEqual(
     *     value="0",
     *     message="TestNumber must be greater or equal to {{ value }}."
     * )
     *
     * @var int
     */
    protected $testNumber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\Term", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="Term can't be blank."
     * )
     *
     * @var Term
     */
    protected $term;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Entity\Group", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="test_group_rel")
     * @Assert\NotBlank(
     *     message="Groups can't be blank."
     * )
     */
    protected $groups;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\Logo", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="Logo can't be blank."
     * )
     *
     * @var Logo
     */
    protected $logo;

    /**
     * @ORM\OneToMany(targetEntity="App\Model\Entity\ProblemTestAssociation", mappedBy="test", cascade={"all"})
     * @Assert\NotBlank(
     *     message="ProblemAssociations can't be blank."
     * )
     */
    protected $problemAssociations;

    /**
     * Test constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->groups = new ArrayCollection();
        $this->problemAssociations = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getIntroductionText(): string
    {
        return $this->introductionText;
    }

    /**
     * @param string $introductionText
     */
    public function setIntroductionText(string $introductionText): void
    {
        $this->introductionText = $introductionText;
    }

    /**
     * @return string
     */
    public function getSchoolYear(): string
    {
        return $this->schoolYear;
    }

    /**
     * @param string $schoolYear
     */
    public function setSchoolYear(string $schoolYear): void
    {
        $this->schoolYear = $schoolYear;
    }

    /**
     * @return int
     */
    public function getTestNumber(): int
    {
        return $this->testNumber;
    }

    /**
     * @param int $testNumber
     */
    public function setTestNumber(int $testNumber): void
    {
        $this->testNumber = $testNumber;
    }

    /**
     * @return Term
     */
    public function getTerm(): Term
    {
        return $this->term;
    }

    /**
     * @param Term $term
     */
    public function setTerm(Term $term): void
    {
        $this->term = $term;
    }

    /**
     * @return mixed
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param mixed $groups
     */
    public function setGroups($groups): void
    {
        $this->groups = $groups;
    }

    /**
     * @return Logo
     */
    public function getLogo(): Logo
    {
        return $this->logo;
    }

    /**
     * @param Logo $logo
     */
    public function setLogo(Logo $logo): void
    {
        $this->logo = $logo;
    }

    /**
     * @return mixed
     */
    public function getProblemAssociations()
    {
        return $this->problemAssociations;
    }

    /**
     * @param mixed $problemAssociations
     */
    public function setProblemAssociations($problemAssociations): void
    {
        $this->problemAssociations = $problemAssociations;
    }

    /**
     * @param ProblemTestAssociation $association
     */
    public function addProblemAssociation(ProblemTestAssociation $association): void
    {
        if($this->problemAssociations->contains($association)) return;
        $this->problemAssociations[] = $association;
    }
}