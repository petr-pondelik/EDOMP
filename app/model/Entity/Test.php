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
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\TestRepository")
 *
 * Class Test
 * @package App\Model\Entity
 */
class Test
{
    use Identifier;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $introductionText;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $schoolYear;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank()
     *
     * @var int
     */
    protected $testNumber;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Assert\NotBlank()
     *
     * @var DateTime
     */
    protected $created;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\Term", cascade={"persist", "merge"})
     *
     * @var Term
     */
    protected $term;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Entity\Group", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="test_group_rel")
     */
    protected $groups;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\Logo", cascade={"persist", "merge"})
     *
     * @var Logo
     */
    protected $logo;

    /**
     * @ORM\OneToMany(targetEntity="App\Model\Entity\ProblemTestAssociation", mappedBy="test", cascade={"all"})
     */
    protected $problemAssociations;

    /**
     * Test constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->created = new DateTime();
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
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return DateTime::from($this->created);
    }

    /**
     * @param DateTime $created
     */
    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
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