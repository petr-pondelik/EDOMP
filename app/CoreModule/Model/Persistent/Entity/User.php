<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 21:46
 */

namespace App\CoreModule\Model\Persistent\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
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
     *
     * @var bool
     */
    protected $isAdmin = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $lastName;

    /**
     * @ORM\ManyToOne(targetEntity="App\CoreModule\Model\Persistent\Entity\Role", inversedBy="users", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="Role can't be blank."
     * )
     *
     * @var Role
     */
    protected $role;

    /**
     * @ORM\ManyToOne(targetEntity="App\CoreModule\Model\Persistent\Entity\User", inversedBy="usersCreated", cascade={"persist", "merge"})
     *
     * @var User
     */
    protected $createdBy;

    /**
     * @ORM\ManyToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\Group", inversedBy="users", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="user_group_rel")
     */
    protected $groups;

    /**
     * @ORM\OneToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\SuperGroup", mappedBy="createdBy", cascade={"all"})
     */
    protected $superGroupsCreated;

    /**
     * @ORM\OneToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\Group", mappedBy="createdBy", cascade={"all"})
     */
    protected $groupsCreated;

    /**
     * @ORM\OneToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\User", mappedBy="createdBy", cascade={"all"})
     */
    protected $usersCreated;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->groups = new ArrayCollection();
        $this->groupsCreated = new ArrayCollection();
        $this->superGroupsCreated = new ArrayCollection();
        $this->usersCreated = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function getCategoriesId(): array
    {
        $res = [];
        foreach($this->groups as $groupKey => $group) {
            foreach ($group->getCategories() as $catKey => $category) {
                if(!in_array($category->getId(), $res)){
                    $res[] = $category->getId();
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
        foreach ($this->getGroups()->getValues() as $key => $group){
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
        if($this->groups->contains($group)){
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
     * @return User
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return mixed
     */
    public function getGroupsCreated()
    {
        return $this->groupsCreated;
    }

    /**
     * @param mixed $groupsCreated
     */
    public function setGroupsCreated($groupsCreated): void
    {
        $this->groupsCreated = $groupsCreated;
    }

    /**
     * @return mixed
     */
    public function getSuperGroupsCreated()
    {
        return $this->superGroupsCreated;
    }

    /**
     * @param mixed $superGroupsCreated
     */
    public function setSuperGroupsCreated($superGroupsCreated): void
    {
        $this->superGroupsCreated = $superGroupsCreated;
    }

    /**
     * @return mixed
     */
    public function getUsersCreated()
    {
        return $this->usersCreated;
    }

    /**
     * @param mixed $usersCreated
     */
    public function setUsersCreated($usersCreated): void
    {
        $this->usersCreated = $usersCreated;
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