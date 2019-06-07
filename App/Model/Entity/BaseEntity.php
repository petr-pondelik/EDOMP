<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 16.5.19
 * Time: 14:49
 */

namespace App\Model\Entity;


use App\Model\Traits\ToStringTrait;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class BaseEntity
 * @package App\Model\Entity
 *
 * @ORM\MappedSuperclass()
 */
abstract class BaseEntity
{
    //Identifier trait for ID column
    use Identifier;

    //Trait for converting entity to string
    use ToStringTrait;

    /**
     * @var string
     */
    protected $toStringAttr = "";

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Assert\NotBlank(
     *     message="Created can't be blank."
     * )
     * @Assert\DateTime()
     *
     * @var DateTime
     */
    protected $created;

    /**
     * BaseEntity constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->created = new DateTime();
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return DateTime
     * @throws \Exception
     */
    public function getCreated(): DateTime
    {
        return DateTime::from($this->created);
    }

    /**
     * @param DateTime $created
     * @throws \Exception
     */
    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
    }
}