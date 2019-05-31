<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 21:46
 */

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\UserRepository")
 *
 * Class User
 * @package App\Model\Entity
 */
class User extends BaseEntity
{
    /**
     * @var string
     */
    protected $toStringAttr = "username";

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(
     *     message="Username can't be blank."
     * )
     *
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(
     *     message="Password can't be blank."
     * )
     * @Assert\Length(
     *     min="8",
     *     exactMessage="Password must be at least 8 chars long."
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
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\User", inversedBy="usersCreated", cascade={"persist", "merge"})
     *
     * @var User
     */
    protected $createdBy;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Entity\Group", inversedBy="users", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="user_group_rel")
     */
    protected $groups;

    /**
     * @ORM\OneToMany(targetEntity="App\Model\Entity\Group", mappedBy="createdBy", cascade={"persist", "merge"})
     */
    protected $groupsCreated;

    /**
     * @ORM\OneToMany(targetEntity="App\Model\Entity\SuperGroup", mappedBy="createdBy", cascade={"persist", "merge"})
     */
    protected $superGroupsCreated;

    /**
     * @ORM\OneToMany(targetEntity="App\Model\Entity\User", mappedBy="createdBy", cascade={"persist", "merge"})
     */
    protected $usersCreated;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\Role", inversedBy="users", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="Role can't be blank."
     * )
     *
     * @var Role
     */
    protected $role;

    /**
     * User constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->groups = new ArrayCollection();
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
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
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
        if($this->groups->contains($group)) return;
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
     * @return array
     */
    public function getCategoriesId(): array
    {
        $res = [];

        foreach($this->groups as $grkey => $group)
        {
            foreach ($group->getCategories() as $catKey => $category)
            {
                if(!in_array($catKey, $res))
                    array_push($res, $catKey);
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
        foreach ($this->getGroups()->getValues() as $key => $group)
            array_push($res, $group->getId());
        return $res;
    }

    /**
     * @return User
     */
    public function getCreatedBy(): User
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

}