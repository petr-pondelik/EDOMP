<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 18:41
 */

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity()
 *
 * Class Test
 * @package App\Model\Entity
 */
class Test
{
    use Identifier;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $introductionText;

    /**
     * @return string
     */
    public function getIntroductionText(): string
    {
        return $this->introductionText;
    }

    /**
     * @param string $introductionText
     */
    public function setIntroductionText(string $introductionText): void
    {
        $this->introductionText = $introductionText;
    }
}