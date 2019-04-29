<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 21:46
 */

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\UserRepository")
 *
 * Class User
 * @package App\Model\Entity
 */
class User
{
    use Identifier;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $password;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Assert\NotBlank()
     *
     * @var DateTime
     */
    protected $created;

    /**
     * User constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->created = new DateTime();
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
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
    }
}