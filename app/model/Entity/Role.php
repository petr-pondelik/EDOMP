<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.5.19
 * Time: 22:03
 */

namespace App\Model\Entity;

use App\Model\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\RoleRepository")
 *
 * Class Role
 * @package App\Model\Entity
 */
class Role extends BaseEntity
{
    use LabelTrait;

    protected $toStringAttr = "label";

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Entity\User", mappedBy="roles", cascade={"all"})
     */
    protected $users;

    /**
     * Role constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->users = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users): void
    {
        $this->users = $users;
    }
}