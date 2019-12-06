<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 16.5.19
 * Time: 14:49
 */

namespace App\CoreModule\Model\Persistent\Entity;


use App\CoreModule\Model\Persistent\Traits\EntitySecurityTrait;
use App\CoreModule\Model\Persistent\Traits\KeyArrayTrait;
use App\CoreModule\Model\Persistent\Traits\ToStringTrait;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class BaseEntity
 * @package App\CoreModule\Model\Persistent\Entity
 *
 * @ORM\MappedSuperclass()
 */
abstract class BaseEntity
{
    // Identifier trait for ID column
    use Identifier;

    // Trait for converting entity to string
    use ToStringTrait;

    // Trait for converting Doctrine ArrayCollection into array of entity keys
    use KeyArrayTrait;

    // Trait for marking entity as secured
    use EntitySecurityTrait;

    /**
     * @var string
     */
    protected $toStringAttr = '';

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
     */
    public function __construct()
    {
        $this->created = DateTime::from('');
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
}