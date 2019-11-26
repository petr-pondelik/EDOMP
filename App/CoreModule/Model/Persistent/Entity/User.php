<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 21:46
 */

namespace App\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Traits\CreatedByTrait;
use App\CoreModule\Model\Persistent\Traits\KeyArrayTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Nette\Security\Passwords;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\CoreModule\Model\Persistent\Repository\UserRepository")
 *
 * Class User
 * @package App\CoreModule\Model\Persistent\Entity
 */
class User extends BaseEntity
{
    use CreatedByTrait;
    use KeyArrayTrait;

    /**
     * @var string
     */
    protected $toStringAttr = 'username';

    /**
     * @ORM\Column(type="string", nullable=false, unique=true, length=128)
     * @Assert\NotBlank(
     *     message="Username can't be blank."
     * )
     * @Assert\Length(
     *     max=128,
     *     maxMessage="Username can't be longer then 128 chars."
     * )
     *
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true, length=128)
     * @Assert\NotBlank(
     *     message="Email can't be blank."
     * )
     * @Assert\Length(
     *     max=128,
     *     maxMessage="Email can't be longer then 128 chars."
     * )
     *
     *
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(
     *     message="Password can't be blank."
     * )
     * @Assert\Length(
     *     min="8",
     *     minMessage="Password must be at least 8 chars long."
     * )
     *
     * @var string
     */
    protected $password;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *     type="bool",
     *     message="IsAdmin must be {{ type }}."
     * )
     *
     * @var bool
     */
    protected $isAdmin = false;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\Type(
     *     type="string",
     *     message="FirstName must be {{ type }}."
     * )
     * @Assert\NotBlank(
     *     message="FirstName can't be blank."
     * )
     *
     * @var string
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\Type(
     *     type="string",
     *     message="LastName must be {{ type }}."
     * )
     * @Assert\NotBlank(
     *     message="LastName can't be blank."
     * )
     *
     * @var string
     */
    protected $lastName;

    /**
     * @ORM\ManyToOne(targetEntity="App\CoreModule\Model\Persistent\Entity\Role")
     * @Assert\NotBlank(
     *     message="Role can't be blank."
     * )
     *
     * @var Role
     */
    protected $role;

    /**
     * @ORM\ManyToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\Group", inversedBy="users", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="user_group_rel")
     *
     * @var ArrayCollection|PersistentCollection
     */
    protected $groups;

    /**
     * Create attributes serves for CLEARING user's authority entities after his DELETION
     * ORDER OF _CREATED ATTRIBUTES IS IMPORTANT!!! IT MUST MATCH ALLOWED DELETION PATH!!!
     */

    /**
     * @ORM\OneToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\Test", mappedBy="createdBy", cascade={"all"})
     *
     * @var ArrayCollection|PersistentCollection
     */
    protected $testsCreated;

    /**
     * @ORM\OneToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal", mappedBy="createdBy", cascade={"all"})
     *
     * @var ArrayCollection|PersistentCollection
     */
    protected $problemFinalsCreated;

    /**
     * @ORM\OneToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate", mappedBy="createdBy", cascade={"all"})
     *
     * @var ArrayCollection|PersistentCollection
     */
    protected $problemTemplatesCreated;

    /**
     * @ORM\OneToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\SuperGroup", mappedBy="createdBy", cascade={"all"})
     *
     * @var ArrayCollection|PersistentCollection
     */
    protected $superGroupsCreated;

    /**
     * @ORM\OneToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\Theme", mappedBy="createdBy", cascade={"all"})
     *
     * @var ArrayCollection|PersistentCollection
     */
    protected $themesCreated;

    /**
     * @ORM\OneToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\Logo", mappedBy="createdBy", cascade={"all"})
     *
     * @var ArrayCollection|PersistentCollection
     */
    protected $logosCreated;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->teacherLevelSecured = true;
        $this->groups = new ArrayCollection();
        $this->superGroupsCreated = new ArrayCollection();
        $this->testsCreated = new ArrayCollection();
        $this->logosCreated = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function getThemesId(): array
    {
        $res = [];
        foreach ($this->groups as $groupKey => $group) {
            foreach ($group->getThemes() as $catKey => $theme) {
                if (!in_array($theme->getId(), $res, true)) {
                    $res[] = $theme->getId();
                }
            }
        }
        return $res;
    }

    /**
     * @return array
     */
    public function getGroupsId(): array
    {
        $res = [];
        foreach ($this->getGroups()->getValues() as $key => $group) {
            $res[] = $group->getId();
        }
        return $res;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @param bool $hash
     */
    public function setPassword(string $password, bool $hash = true): void
    {
        $this->password = $hash ? Passwords::hash($password) : $password;
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
        if ($this->groups->contains($group)) {
            return;
        }
        $this->groups[] = $group;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * @param bool $isAdmin
     */
    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * @param Role $role
     */
    public function setRole(Role $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(string $firstName = null): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(string $lastName = null): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}