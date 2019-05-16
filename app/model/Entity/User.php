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
     * @ORM\ManyToMany(targetEntity="App\Model\Entity\Group", inversedBy="users", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="user_group_rel")
     */
    protected $groups;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Entity\Role", inversedBy="users", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="user_role_rel")
     * @Assert\NotBlank(
     *     message="Roles can't be blank."
     * )
     */
    protected $roles;

    /**
     * User constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->groups = new ArrayCollection();
        $this->roles = new ArrayCollection();
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
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @param Role $role
     */
    public function addRole(Role $role): void
    {
        if($this->roles->contains($role)) return;
        $this->roles[] = $role;
    }

    /**
     * @return array
     */
    public function getRolesId(): array
    {
        $res = [];
        foreach ($this->getRoles()->getValues() as $key => $role)
            array_push($res, $role->getId());
        return $res;
    }

    /**
     * @return array
     */
    public function getRolesLabel(): array
    {
        $res = [];
        foreach ($this->getRoles()->getValues() as $key => $role)
            array_push($res, $role->getLabel());
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
}