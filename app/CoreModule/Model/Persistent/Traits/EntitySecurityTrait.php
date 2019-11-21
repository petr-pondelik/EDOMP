<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.11.19
 * Time: 14:32
 */

namespace App\CoreModule\Model\Persistent\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trait EntitySecurityTrait
 * @package App\CoreModule\Model\Persistent\Traits
 */
trait EntitySecurityTrait
{
    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *     type="bool",
     *     message="TeacherLevelSecured must be {{ type }}."
     * )
     *
     * @var bool
     */
    protected $teacherLevelSecured = false;

    /**
     * @return bool
     */
    public function isTeacherLevelSecured(): bool
    {
        return (bool) $this->teacherLevelSecured;
    }

    /**
     * @param bool $teacherLevelSecured
     */
    public function setTeacherLevelSecured(bool $teacherLevelSecured): void
    {
        $this->teacherLevelSecured = $teacherLevelSecured;
    }
}