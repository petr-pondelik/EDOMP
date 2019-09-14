<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 18:41
 */

namespace App\Model\Persistent\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\TestRepository")
 *
 * Class Test
 * @package App\Model\Persistent\Entity
 */
class Test extends BaseEntity
{
    /**
     * @var string
     */
    protected $toStringAttr = 'id';

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
     *     message="TestNumber must be greater or equal to 0."
     * )
     *
     * @var int
     */
    protected $testNumber;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(
     *     message="Term can't be blank."
     * )
     *
     * @var string
     */
    protected $term;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Persistent\Entity\Group", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="test_group_rel")
     * @Assert\NotBlank(
     *     message="Groups can't be blank."
     * )
     *
     * @var Group[]
     */
    protected $groups;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Persistent\Entity\Logo", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="Logo can't be blank."
     * )
     *
     * @var Logo
     */
    protected $logo;

    /**
     * @ORM\OneToMany(targetEntity="App\Model\Persistent\Entity\TestVariant", mappedBy="test", cascade={"all"})
     * @Assert\NotBlank(
     *     message="TestVariants can't be blank."
     * )
     *
     * @var TestVariant[]
     */
    protected $testVariants;

    /**
     * Test constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->groups = new ArrayCollection();
        $this->testVariants = new ArrayCollection();
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
     * @return string
     */
    public function getTerm(): string
    {
        return $this->term;
    }

    /**
     * @param string $term
     */
    public function setTerm(string $term): void
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
     * @param Group $group
     */
    public function addGroup(Group $group): void
    {
        if($this->groups->contains($group)){
            return;
        }
        $this->groups[] = $group;
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
     * @return TestVariant[]|ArrayCollection
     */
    public function getTestVariants()
    {
        return $this->testVariants;
    }

    /**
     * @param TestVariant[] $testVariants
     */
    public function setTestVariants(array $testVariants): void
    {
        $this->testVariants = $testVariants;
    }

    /**
     * @param TestVariant $testVariant
     */
    public function addTestVariant(TestVariant $testVariant): void
    {
        if($this->testVariants->contains($testVariant)){
            return;
        }
        $this->testVariants[] = $testVariant;
    }
}