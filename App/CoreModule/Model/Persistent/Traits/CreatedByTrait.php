<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.11.19
 * Time: 19:31
 */

namespace App\CoreModule\Model\Persistent\Traits;

use App\CoreModule\Model\Persistent\Entity\User;

/**
 * Trait CreatedByTrait
 * @package App\CoreModule\Model\Persistent\Traits
 */
trait CreatedByTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="App\CoreModule\Model\Persistent\Entity\User", cascade={"persist", "merge"})
     *
     * @var User|null
     */
    protected $createdBy;

    /**
     * @return User|null
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * @param User|null $createdBy
     */
    public function setCreatedBy(?User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }
}